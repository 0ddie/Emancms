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

The **script** is organized in **stages**. A stage finishes when the script has to wait for *something* to happen (ie. wait for a feed to be updated). The next stage will be triggered when that *something* has hapenned or we have gone beyond the **timeout**.

The **Rules Schedule** cron task that runs on the backend is the one that controls when a rule is run and also when the next stage is triggered.

##Rules programmer

The **rules programmer** can be found In the user interface for adding/editing a rule.

It is based on the marvellous library [Morphic.js](https://github.com/jmoenig/morphic.js) and also uses code from [Snap!](https://snap.berkeley.edu/) and amazing visual, drag-and-drop programming language. Both developed by [Jens Mönig](https://github.com/jmoeni). The **rules programmer** would have never been possible to implement without them.

When a rule is saved, xml code representing the blocks in the script is generated and stored in the database. The **Rules Schedule** will translate the xml to php code when a rule is run. This is important as this can be a big security hole. Great attention is to be put in sanitazing the blocks to avoid code injection.

The potential of the rules programmer is great. Tutorials about how to add new functionality will hopefully be written next time I implement anything from the ToDo list below.

##ToDo list
- Add time picker to "expiry_time" and "run_on"
- Look and feel nicer
- Add license
- Fix bugs: 
	- Fundefined
	- Superbig button when choosen attributes or feeds
- Add feed: first dialog shows list of tags instead nodes (Michael should put the node number into the feed tag when the register module creates the feed for attribute) 
- Add attribute: ideally Michael would implement tags for the Attributes, if this happens the first dialog to *add attribute* should display tags.
- Make "comparators" blocks with corners instead of round borders
- Add "want drop of" to blocks in order to avoiddropping the wrong blocks in a command
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
	- Operators
		- Addition, times, subtraction, division
	- Reporters
		- Add Rule
		- Lists (and other blocks to use them)
		- True and false
		- Timeout (secs)
		
		