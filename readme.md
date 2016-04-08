This is a basic plugin that passes customers first name, last name and email address to Mautic. It is a very rough implementation, which is the result of me (a Node developer) knowing very little about the WP platform or PHP. However, it does work (at least for me!).

Please do jump in with a PR if you are able to improve something!

<h2>Install</h2>
The same way you would any other wordpress plugin. FTP/SSH to wp-content/plugins. More info on how to install plugins here: <a href="https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation">https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation</a>

<h2>Set up</h2>

1. Create a new Mautic form that will be used to submit woocommerce customers. 
2. On the new form add the following fields: 
	first_name - text field, Matching lead field = First Name
	last_name - text field, Matching lead field = Last Name
	email - email field, Matching lead field = Email
3. Save your form and make a note of the ID of the form.
4. Install the Mautic Woocommerce plugin and activate it.
5. In WP (from the dashboard) got to Settings>WC Mautic and enter in the url of your copy of Mautic and the ID of the form as previously noted.
6. Save and test. Enjoy!

If you feel that my plugin has helped you, and you would like to make a kind donation for my work then please do :)

<a href='https://pledgie.com/campaigns/31430'><img alt='Click here to lend your support to: Woocommerce Mautic Plugin and make a donation at pledgie.com !' src='https://pledgie.com/campaigns/31430.png?skin_name=chrome' border='0' ></a>
