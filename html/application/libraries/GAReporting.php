<?php

Class GAReporting
{

  /**
   * Facebook constructor.
   */
  public function __construct()
  {
  }


  /**
  * Creates and returns the Analytics Reporting service object.
  */
  function init($viewid)
  {
    // Use the developers console and download your service account
    // credentials in JSON format. Place them in this directory or
    // change the key file location if necessary.
    $KEY_FILE_LOCATION = APPPATH . 'config/gareporting.json';
    $this->viewid = $viewid;

    // Create and configure a new client object.
    $this->client = new Google_Client();
    $this->client->setApplicationName("");
    $this->client->setAuthConfig($KEY_FILE_LOCATION);
    $this->client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
    $this->analytics = new Google_Service_AnalyticsReporting($this->client);

    return $this->analytics;
  }


  // requête Google Analytics simple
  function request($metrics, $dimensions, $startDate, $endDate, $dimensionfilters = NULL)
  {
    // Create the DateRange object.
    $dateRange = new Google_Service_AnalyticsReporting_DateRange();
    $dateRange->setStartDate($startDate);
    $dateRange->setEndDate($endDate);

    // Create the Metrics object.
    $metrics_array = array();
    foreach($metrics as $metric)
    {
      $metrics_elt = new Google_Service_AnalyticsReporting_Metric();
      $metrics_elt->setExpression('ga:'.$metric);
      $metrics_elt->setAlias($metric);
      $metrics_array[] = $metrics_elt;
    }


    // Dimensions
    $dimensions_array = array();
    foreach($dimensions as $dimension)
    {
      $dimensions_elt = new Google_Service_AnalyticsReporting_Dimension();
      $dimensions_elt->setName('ga:'.$dimension);
      $dimensions_array[] = $dimensions_elt;
    }

    // Filtre éventuel
    $dimensionfilters_array = array();
    if (isset($dimensionfilters))
    {
      foreach($dimensionfilters as $dimensionfilter)
      {
        $filter_elt = new Google_Service_AnalyticsReporting_DimensionFilter();
        $filter_elt->setDimensionName('ga:'.$dimensionfilter[0]);
        $filter_elt->setOperator($dimensionfilter[1]);
        $filter_elt->setExpressions($dimensionfilter[2]);
        $dimensionfilters_array[] = $filter_elt;
      }
    }


    // Create the ReportRequest object.
    $request = new Google_Service_AnalyticsReporting_ReportRequest();
    $request->setDateRanges($dateRange);
    $request->setMetrics($metrics_array);
    if (sizeof($dimensions_array)>0)
      $request->setDimensions($dimensions_array);
    if (sizeof($dimensionfilters_array)>0)
    {
      $dimensionFilterClause = new Google_Service_AnalyticsReporting_DimensionFilterClause();
      $dimensionFilterClause->setFilters($dimensionfilters_array);
      $request->setDimensionFilterClauses($dimensionFilterClause);
    }
    $request->setViewId($this->viewid);

    $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
    $body->setReportRequests( array( $request) );
    
    // requêtes
    $reports = $this->analytics->reports->batchGet( $body );
    //pr($reports);

    // structuration des résultats
    $result = array();
    for ( $reportIndex = 0; $reportIndex < count( $reports ); $reportIndex++ ) 
    {
      $report = $reports[ $reportIndex ];
      $header = $report->getColumnHeader();
      $dimensionHeaders = $header->getDimensions();
      $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
      $rows = $report->getData()->getRows();
      for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++) 
      {
        $row = $rows[ $rowIndex ];
        $data = array();

        $dimensions = $row->getDimensions();
        for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) 
          $data[] = $dimensions[$i];

        $metrics = $row->getMetrics();
        for ($j = 0; $j < count( $metricHeaders ) && $j < count( $metrics ); $j++) 
        {
          $values = $metrics[$j]->getValues();
          for ( $valueIndex = 0; $valueIndex < count( $values ); $valueIndex++ ) 
            $data[] = $values[ $valueIndex ];
        }

        $result[] = $data;
      }
    }
    //pr($result);

    
    return $result;
  }


  // récupération des données de rétention
  function getRetention($type, $count)
  {
    // Create the Metrics object.
    $metrics_array = array();
    foreach(array('cohortActiveUsers','cohortTotalUsers') as $metric)
    {
      $metrics_elt = new Google_Service_AnalyticsReporting_Metric();
      $metrics_elt->setExpression('ga:'.$metric);
      $metrics_elt->setAlias($metric);
      $metrics_array[] = $metrics_elt;
    }

    // Dimensions
    $dimensions = array('cohort');
    if ($type==0)
      $dimensions[] = 'cohortNthDay';
    else if ($type==1)
      $dimensions[] = 'cohortNthWeek';
    else if ($type==2)
      $dimensions[] = 'cohortNthMonth';

    $dimensions_array = array();
    foreach($dimensions as $dimension)
    {
      $dimensions_elt = new Google_Service_AnalyticsReporting_Dimension();
      $dimensions_elt->setName('ga:'.$dimension);
      $dimensions_array[] = $dimensions_elt;
    }

    // Cohorts
    // A valid cohort request must meet the following restrictions:
    // The ga:cohort dimension is included if and only if the request has one or more cohort definitions.
    // The cohort name must be unique.
    // The maximum number of cohorts in a request is 12.
    // If ga:cohortNthWeek is defined, the start date must be Sunday and the end date must be Saturday. 
    // If ga:cohortNthMonth is defined, the start date must be the first day of the month 
    // and the end date must be the last day of the month. 
    // If ga:cohortNthDay is defined, the date range must be exactly one day.
    // Cohort requests with today's date are not allowed.
    // Cohort and non-cohort requests should not be in the same batchGet request.
    // The date range in cohorts must be after February 1, 2015.

    $cohorts = array();
    $start = new DateTime();
    if ($type==0)
    {
      // on ramène un certain nombre de jour en arrière
      $start->sub(new DateInterval('P'.$count.'D'));
      $end = clone $start;
    }
    else if ($type==1)
    {
      // on ramène au dimanche de la semaine
      while($start->format('w') != 0)
        $start->sub(new DateInterval('P1D'));
      // on repart un certain nombre de semaines en arrière
      $start->sub(new DateInterval('P'.($count*7).'D'));

      $end = clone $start;
      $end->add(new DateInterval('P6D'));
    }
    else if ($type==2)
    {
      // on ramène au début du mois
      $start = DateTime::createFromFormat('Y-m-d', $start->format('Y-m-1'));
      // on repart un certain nombre de mois en arrière
      $start->sub(new DateInterval('P'.$count.'M'));

      $end = clone $start;
      $end->add(new DateInterval('P1M'));
      $end->sub(new DateInterval('P1D'));
    }
    
    //pr($start->format('Y-m-d'));
    //pr($end->format('Y-m-d'));

    
    for($i=0;$i<=$count;$i++)
    {
      $cohort = new Google_Service_AnalyticsReporting_Cohort();
      $cohort->setName("cohort".$i);
      $cohort->setType("FIRST_VISIT_DATE");
      $range = new Google_Service_AnalyticsReporting_DateRange();
      $range->setStartDate($start->format('Y-m-d'));
      $range->setEndDate($end->format('Y-m-d'));

      //pr($start->format('Y-m-d'));
      //pr($end->format('Y-m-d'));

      if ($type==0)
      {
        $start->add(new DateInterval('P1D'));
        $end->add(new DateInterval('P1D'));
      }
      else if ($type==1)
      {
        $start->add(new DateInterval('P7D'));
        $end->add(new DateInterval('P7D'));
      }
      else if ($type==2)
      {
        $start->add(new DateInterval('P1M'));
        $end = clone $start;
        $end->add(new DateInterval('P1M'));
        $end->sub(new DateInterval('P1D'));
      }
      
      $cohort->setDateRange($range);
      $cohorts[] = $cohort;
    }

    $cohortGroup = new Google_Service_AnalyticsReporting_CohortGroup();
    $cohortGroup->setCohorts($cohorts);

    // Create the ReportRequest object.
    $request = new Google_Service_AnalyticsReporting_ReportRequest();
    $request->setViewId($this->viewid);
    //$request->setDateRanges($dateRange);
    $request->setMetrics($metrics_array);
    $request->setDimensions($dimensions_array);
    $request->setCohortGroup($cohortGroup);

    $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
    $body->setReportRequests( array( $request) );
    
    // requêtes
    $reports = $this->analytics->reports->batchGet( $body );
    //pr($reports);

    // prefixe pour label
    if ($type==0)
      $prefix = "Jour ";
    else if ($type==1)
      $prefix = "Semaine ";
    else if ($type==2)
      $prefix = "Mois ";

    // structuration des résultats
    $result = array();
    $totalUsers = array();
    for($i=0;$i<=$count;$i++)
    {
      $result[$prefix.$i] = array(0,0);
      $totalUsers["cohort".$i] = 0;
    }

    //pr($reports);
    for ( $reportIndex = 0; $reportIndex < count( $reports ); $reportIndex++ ) 
    {
      $report = $reports[ $reportIndex ];
      $header = $report->getColumnHeader();
      $dimensionHeaders = $header->getDimensions();
      $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
      $rows = $report->getData()->getRows();

      for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++) 
      {
        $row = $rows[ $rowIndex ];

        $dimensions = $row->getDimensions();
        $index = intval($dimensions[1]);
        $metrics = $row->getMetrics()[0]->getValues();

        $result[$prefix.$index][0] += $metrics[0];
        $result[$prefix.$index][1] += $metrics[1];

        // récupération du nombre total d'utilisateurs inscrit à chaque cohort
        if ($index==0)
          $totalUsers[$dimensions[0]] = $metrics[1];
      }
    }

    for($i=0;$i<=$count;$i++)
    {
      // calcul du total d'utilisateur sur ce cohort là 
      // (en ignorant ceux qui se sont inscrit trop récemment)
      $totalUsersForDate = 0;
      for($j=0;$j<=($count-$i);$j++)
        $totalUsersForDate+=$totalUsers["cohort".$j];

      if ($totalUsersForDate>0)
        $result[$prefix.$i][0] = Round(10000*floatval($result[$prefix.$i][0]/$totalUsersForDate))*0.01;
    }

    return $result;
  }


}