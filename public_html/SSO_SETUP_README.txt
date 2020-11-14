1. Copy simplesamlphp directory attached to this email to /var/www/html/leavemanagement/public_html diretory.
2. Visit this url for Instructions -
http://www.lewisroberts.com/2015/09/05/single-sign-on-to-azure-ad-using-simplesamlphp/
3. Apache Configuration -
 Open /etc/apache2/sites-available/leavemanagement

Add Line after 
ServerName ansysleave
and before DocumentRoot /var/www/html/leavemanagement/public_html line

Alias /simplesaml /var/www/html/simplesamlphp/www

================== Full code will look like this -
<VirtualHost *:80>
        ServerAdmin webmaster@localhost
        ServerName ansysleave

Alias /simplesaml /var/www/html/leavemanagement/public_html/simplesamlphp/www
        DocumentRoot /var/www/html/leavemanagement/public_html

        <Directory /var/www/html/leavemanagement>
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                allow from all
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/leavemanagement-error.log

        # Possible values include: debug, info, notice, warn, error, crit,
        # alert, emerg.
        LogLevel warn

        CustomLog ${APACHE_LOG_DIR}/leavemanagement-access.log combined
</VirtualHost>

4. Restart the apache server. Add to /etc/hosts file. Entry for simplesaml.

5. Access the browser -
	http://ansysleave/simplesaml/
6. Welcome screen will open.


Import federation data from Azure application to SimpleSAMLphp -

1. Open the Windows Azure Management portal and navigate to your application. Click View Endpoints in the grey banner at the bottom.
2. In the App Endpoints window. Copy the URL for the Federation Metadata Document.
3. Using your favourite browser, navigate to the location and save the metadata document. How you do this doesn’t really matter, as long as it’s just the XML you save.
4. Open the federationmetadata.xml file in a text editor, select the entire contents (Ctrl+A) and then copy it to the clipboard.
5. Open the browser and nevigate to 
http://ansysleave/simplesaml
6. Click on Federation Tab (Last Tab)
7. On the Federation tab, look for Tools and then click XML to simpleSAMLphp metadata converter.
8. Paste the entire federationmetadata.xml file’s contents in to the field and click the Parse button.
9. The page should return almost immediately with some information similar to the following under the Converted metadata section.
10. Copy the contents of the saml20-idp-remote field to simplesamlphp/metadata/saml20-idp-remote.php file in the end.


Configure SimpleSAMLphp as a service provider -

1. On konsole go to simplesamlphp/config directory.
2. Open authsource.php file.
3. There are a number of authentication sources preconfigured (but commented out) however the one we’re interested in (or rather, its general format) is default-sp.
4. Firstly, change the entityID value to reflect the name or URL of your Azure application.
5. Next, enter the idp value. The very first line of the converted metadata actually gives you the IdP (Identity Provider) – in this case, Azure AD.
6. discoURL stays as null.
7. Next we must add some additional information that is required to communicate with Azure AD. Underneath discoURL, add the following two lines.
'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
'simplesaml.nameidattribute' => 'eduPersonTargetedID',

8. We’re complete editing authsources.php so save and close the file.


Testing authentication

1. Navigate to http://ansysleave/simplesaml
2. Click on Authentication tab
3. Once there, click Test configured authentication sources
4. We should see only two options, admin and default-sp. These were the only two authentication sources defined in authsources.php. Click default-sp.
5. It you get error see the Reply Url in the error.
6. First, copy the URL from the error. In this case, the URL is: https://ansysleave/simplesaml/module.php/saml/sp/metadata.php/default-sp  
7. In the Azure Management portal, find the application, scroll to Single Sign-On and add it to the list of Reply URLs. Save the configuration change but leave the management portal open in case you must make any more edits. 
8. Close the other browser showing the error, open another and repeat steps 1 to 3.
After clicking on default-sp now that we have added the Reply URL in to the Windows Azure portal, we should be shown the Sign in page.
Notice the URL we’re sent to. This is the actually the SAML-P Sign On Endpoint for the Azure application.
9. Log in with a user account that’s in your Azure Active Directory.
10. We should now be able to sign in without error and get redirected back to SimpleSAMLphp and shown a list of the claims that were sent along with the authentication.
11. To test logging out, click Logout.


Custom PHP application code

1. I have created index.php file with the 'print_r($attributes);' line. This array will give us logged in user's detials.
2. We need to use that array and set the user_id.

