#Rules Module

##Intro
The **Rules Module** is a **User Interface** and a **Cron task** (aka *Rules Schedule*) to allow **EmonCMS** users build sets of actions (aka *Rules*) to interact with feeds and nodes. It is currently on development stage.

This module is part of the **OpenEnergyManagement (OEMan)** system that the [Centre for Alternative Technology](http://cat.org.uk) (**CAT**) is developing for the **OpenEnergyMonitor** project. OEMan has been devised by **Adam Tyler**, bright heating engineer in CAT's Engineering department. The module itself is being developed by myself [Carlos Alonso Gabizón](https://github.com/cagabi)

The Rules Module allows users to send control statements to nodes. So far *control* has been implemented for nodes that use the Register Module that [Michael Oddie](https://github.com/0ddie) has developed as part of OEMan and can be found in this same repository. This is the only bespoke developement. Everything else works without the need of the Register Module, this was one of the main aims of this Rules Modules: **be a contribution to the OpenEnergyMonitor** but not only to OEMan.

##Installing the module
Just copy and paste the Rules module's folder in "/Modules" and (logged in as administrator) update emonCMS database (admin tab on the top right)

##What is a rule
A *rule* is a set of actions that are run according to a schedule. A rule is defined by the **next time to run** (aka *Run on*), the **frequency**, an **expiry date** and the  **script**.

The **script** is made with a visual programmer. No coding skills are required to be able to *build* a script, it is just a drag and drop application where functional blocks are added to each other.

The **script** is organized in **stages**. A stage finishes when the script has to wait for *something* to happen like waiting for a feed to be updated, in this case a pending acknowledgement is generated and added to the database. The next stage will be triggered when that *something* has hapenned (we have received the ack) or we have gone beyond the **timeout**.

##Rules Schedule

The **Rules Schedule** is a cron job triggered every second. Its mission is to run the rules that are enabled and have reached their time to run. It also sets the next time the rule should be run according to the frecuency and expiry date defined by the user for the rule.

Part of running a rule is checking if the pending acks have been received and trigger the next stage of the rule when it happens.

##Rules programmer

The **rules programmer** can be found In the user interface for adding/editing a rule.

It is based on the marvellous library [Morphic.js](https://github.com/jmoenig/morphic.js) and also uses code from [Snap!](https://snap.berkeley.edu/) and amazing visual, drag-and-drop programming language. Both developed by [Jens Mönig](https://github.com/jmoeni). The **rules programmer** would have never been possible to implement without them.

When a rule is saved, xml code representing the blocks in the script is generated and stored in the database. The **Rules Schedule** will translate the xml to php code when a rule is run. This is important as this can be a big security hole. Great attention is to be put in sanitazing the blocks to avoid code injection.

The potential of the rules programmer is great. Tutorials about how to add new functionality will hopefully be written next time I implement anything from the ToDo list below.

##Notes

In order to fully understand how the Rules module works I want to add this notes hoping they are of any help:

- When we do any kind of request a pending ack is added to the database
- When the timeout of a pending ack is reached, all the other pending acks for that rule are deleted and the next stage is run with the $timedout variable set to true.
- If at the time of running a rule there are still pending acks for it, the rule will not be run. This situation will happen when the frequency for the rule is less than the timeout. For example, if the rule's frequency is 5 secs but we allow 1 minute for the timeout of the pending acks, it may be the case that we are still waiting for the ack when try to run the rule again.
- When testing a rule, the pending acks are added to a different table than the one we use when running rules normally.
- We have included to variables in settings.php:
    - $rules_developer_mode: when set true will print usefuls messages in the view
    - $rules_log_mode: when set to *verbose*, the module will log into file basically warnings and info. When set to something different only errors (warns) will be logged.

##ToDo list
- Add time picker to "expiry_time" and "run_on"
- Look and feel nicer
- Add license
- ~~Fix bugs:~~ 
	- ~~Fundefined~~
	- ~~Superbig button when choosen attributes or feeds~~
- ~~Add feed: first dialog shows list of tags instead nodes (Michael should put the node number into the feed tag when the register module creates the feed for attribute) ~~
- Add attribute: ideally Michael would implement tags for the Attributes, if this happens the first dialog to *add attribute* should display tags.
- Make "comparators" blocks with corners instead of round borders
- Add "want drop of" to blocks in order to avoid dropping the wrong blocks in a command
- Stop displaying the dialog that pops everytime you want to leave the page
- Not focus on the programmer when page loads
- Tutorial *How to add functionality new functionality* (when implement any of the new functionality below)
- Autodetect if the Register Module is installed and if it is add Attributes to the reporters pane. 
- Add functionality (new blocks)
	- Controls: 
		- send email to *xxxx* saying *xxxxx*
		- log into file - message *xxxxx*
		- set rule *xxxx* frequency to *xxxx*
		- set rule *xxxx* run on to *xxxx*
		- set rule expiry date to *xxxx*
		- wait *xxx* secs
                - see_timeout()
	- Operators
		- Addition, times, subtraction, division
	- Reporters
		- Add Rule
		- Lists (and other blocks to use them)
		- True and false
		- Timeout (secs)
		
		