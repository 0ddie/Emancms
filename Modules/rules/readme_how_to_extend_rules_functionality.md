#How to extend the functionality of the Rules module
What I mean when saying *extending the functionality of the Rules module* means to add new commands, operators or reporters to the rules programmer. This additions are done first into the rules programmer (JavaScript) but also we need to add some new code to the methods in the Rules model (php) that generate the php code for a rule, add the *pending acks* to the database and also check if those *acks* have been received. So adding functionality to the Rules Module implies adding code to different files in different places. But don't be scared, i hope this documentation will help you in this mission ;-)

This document is structured in three sections: *ToDo list*, *How to add new blocks to the rules programmer* and *The new blocks in the Rules model*. The later will explain how to modify rules model in order to be able to handle the new blocks.


##ToDo list
- How to add a bloc to add reporters like *Add feed*
- Expand everything said in the document. So far it is a good guide because it tells where to add the code but it relies in the good willing of developer to check out the code that is there already in order to understand how everything works.

##How to add new blocks to the rules programmer

The new type of blocks we can add are:

 - Commands: blocks that *do* something but don't return anything. Example of commands are: if,  if/else, requestFeed.
 - Reporters: blocks that *do* something and return something. An example is getLastFeed.
 - Operators: like "=" or "<"

The difference between a *command* and a *reporter* may not be obvious but this example will help:

 - requestFeed(feedid) does everything needed to send a request to a node so that it sends back the last value it meassured. The requestFeed block is a command because it is not returning anything itself.
 - getLastFeed(feedid) fetches from the database the last value of the feed and returns it.

So the first thing you need to know is what kind of block you want then in in [scripts/emonCMS_RulesIDE_gui.js](scripts/emonCMS_RulesIDE_gui.js) add the name of your new block to:

- For commands and reporters:  `addBlocksToControlsTemplatesPane` method 
- For an operator: `addBlocksToOperatorsTemplatesPane` method

In this methods you will have to add the selector (name that identifies the block) to an array. If that block has already been defined somewhere else you are done but if not you will have to define it yourself.

The list of blocks available in [Snap!](https://snap.berkeley.edu/) can be found in [scripts/objects.js](scripts/objects.js) in the `SpriteMorph.prototype.initBlocks` method. If you are creating a new block instead of reusing one from Snap!, add it to [emonCMS_RulesObjects.js](emonCMS_RulesObjects.js). You will hate me for saying this but the only way to understand how to define a new block is to have a look at the existing blocks in Snap! and what they do, then check out how they are defined in the files mentioned above. Sorry this is what i had to do and it is still not totally clear for me ;-)

Once you have done this, your block will be there!!!

##The new blocks in the Rules model

The blocks are stored in the database as xml and are translated to php by the `blocksToPhp($block)` method in [rules_model.php](rules_model.php). As you can see there, you will have to add the selector of your new block to the switch statement and generate a string with the php code for it. Be aware that `$block` is a SimpleXMLElement object and it is quite confusing how to handle it, use the developer mode to se a print in screen and help you.

###Pending acks

In the case of *command* blocks there is the chance to do something that requires to wait until we get a reply (for example when requesting a node to update an input or feed). In this case we need to **generate a pending ack**, just add the folowing line:
```
$this->addPendingAck("selectorOfTheBlock", $ruleid, $stage + 1, ["thisIsAnArgument"=>' . $the_value_of_the_argument . '], $timeout);
```
And as an example, this is how we the *requestFeed* adds a pending ack:
```
$this->addPendingAck("requestFeed", $ruleid, $stage + 1, ["feedid"=>' . $feed_id . '], $timeout);
```
Next thing to do is to add the code to **check if the ack has been received**. This is done in the method `checkAck($pending_ack)`.

Again you will have to add the selector of your block to the switch statement and you will be to use `$args['thisIsAnArgument']` to retrieve the arguments you passed when generating the pending ack.

You must return an associative array in the form `['success' => (int), 'message' => ''];`

Where succes can be:

- 0: ack not received
- 1: ack received
- Any other number: there has been error, what you put in `message` will be logged to the log file and printed on the screen if you are testing the rule.

