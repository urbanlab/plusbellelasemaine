<?php echo $this->load->view('emailer/includes/emailHeader'); ?>

<div class="block">
   <!-- start textbox-with-title -->
   <table width="100%" bgcolor="#DDDDDD" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="fulltext">
      <tbody>
         <tr>
            <td>
               <table bgcolor="#ffffff" width="580" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" modulebg="edit">
                  <tbody>
                     <!-- Spacing -->
                     <tr>
                        <td width="100%" height="15"></td>
                     </tr>
                     <!-- Spacing -->
                     <tr>
                        <td>
                           <table width="540" align="center" cellpadding="0" cellspacing="0" border="0" class="devicewidthinner">
                              <tbody>
                                 <!-- Title -->
                                 <tr>
                                    <td style="font-family: arial, sans-serif; font-size: 22px; font-weight:bold; color: #000000; text-align:left;" st-title="fulltext-title">
                                       Renouvellement de votre mot de passe
                                    </td>
                                 </tr>
                                 <!-- End of Title -->
                                 <!-- spacing -->
                                 <tr style="border-bottom:1px solid #eaeaea;">
                                    <td height="10"></td>
                                 </tr>
                                 <!-- End of spacing -->
                                 <!-- spacing -->
                                 <tr>
                                    <td height="15"></td>
                                 </tr>
                                 <!-- End of spacing -->
                                 <!-- content -->
                                 <tr>
                                    <td style="font-family: arial, sans-serif; font-size: 15px; color: #000000; text-align:left;" st-content="fulltext-paragraph">
                                       <strong>Votre nouveau mot de passe est</strong> : <?php if (isset($newPassword)) echo $newPassword; ?><br>
									   <a href="<?php echo base_url(); ?>admin">S'identifier</a>
                                    </td>
                                 </tr>
                                 <!-- End of content -->
                                 <!-- Spacing -->
                                 <tr>
                                    <td width="100%" height="15"></td>
                                 </tr>
                                 <!-- Spacing -->
                                 
                              </tbody>
                           </table>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </td>
         </tr>
      </tbody>
   </table>
   <!-- end of textbox-with-title -->
</div>

<?php echo $this->load->view('emailer/includes/emailFooter'); ?>
