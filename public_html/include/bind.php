<?php

$ds=ldap_connect("pundc2.win.ansys.com","389") or die ("LDAP Error Connection!!!");
if ($ds) {
ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);	
   $r=ldap_bind($ds,'ansys\cjayapra','') or die ("Couldn't bind to AD! --->" .ldap_error($ds)); 
   
   $uid="rlakshma";
  # $uid="cjayapra";
   $uid="dmreddy";
   $attributes = array("displayname", "mail","manager");
   $filter="(sAMAccountName=$uid)";
   $sr=ldap_search($ds, "OU=Standard,OU=Users,OU=Bangalore,OU=RG - India,DC=win,DC=ansys,DC=com", $filter); 
   $entries = ldap_get_entries($ds, $sr);
   $nm=$entries[0]["displayname"][0];
   echo $nm."hhhhhhhhhhhh<br>".$entries[0]["displayname"][0]."<br>".$entries[0]["mail"][0]."<br />-------------".$entries[0]["manager"][0]."<br />";
   $filter="(sAMAccountName=*)";
   $sr=ldap_search($ds, $entries[0]["manager"][0],$filter); 
   $entries_m = ldap_get_entries($ds, $sr); 
   echo "My Manager: ". $entries_m[0]["mail"][0];
   echo "<br> List If user is Manager<br>";
   $filter="(directReports=*)";
   $sr=ldap_search($ds, "cn=$nm,OU=Standard,OU=Users,OU=Pune,OU=RG - India,DC=win,DC=ansys,DC=com", $filter); 
   $entries_d = ldap_get_entries($ds, $sr);
   
   foreach($entries_d as $val)
		{
		 foreach($val as $key=>$val1)
		 {
			if($key=="directReports")	
			{

				foreach($val1 as $key2=>$val2)
				{
					echo $entries_d;
				}
				
			}
		 }
		}
   
}
ldap_unbind($ds);
?> 
