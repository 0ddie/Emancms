var emonCMS_RulesIDE_Morph;

// emonCMS_RulesIDE_Morph ///////////////////////////////////////////////////////////

// I am emonCMS_Rules top-level frame, the Editor window

// emonCMS_RulesIDE_Morph inherits from Morph:

emonCMS_RulesIDE_Morph.prototype = new Morph();
emonCMS_RulesIDE_Morph.prototype.constructor = emonCMS_RulesIDE_Morph;
emonCMS_RulesIDE_Morph.uber = Morph.prototype;

// IDE_Morph instance creation:

function emonCMS_RulesIDE_Morph(width, height, array_of_feeds_by_tag, array_of_attributes_by_node, blocks) {
    this.init(width, height, array_of_feeds_by_tag, array_of_attributes_by_node, blocks);
}

emonCMS_RulesIDE_Morph.prototype.init = function (width, height, array_of_feeds_by_tag, array_of_attributes_by_node, blocks) {
    var myself = this;

    // initialize setting and properties
    MorphicPreferences.globalFontFamily = 'Helvetica, Arial';
    this.color = new Color(0, 32, 5); //not working
    emonCMS_RulesIDE_Morph.uber.init.call(this); // initialize inherited properties
    this.setWidth(width);
    this.setHeight(height);
    this.array_of_feeds_by_tag = array_of_feeds_by_tag;
    this.array_of_attributes_by_node = array_of_attributes_by_node;
    this.blocks_string = blocks;
    if (this.blocks_string !== null)
        this.loadRule = true;
    else
        this.loadRule = false;

    //the three panes in the IDE
    this.categoriesPane = new Morph();
    this.scriptsPane = new ScriptsMorph();
    this.blockTemplatesPane = new ScrollFrameMorph();
    this.add(this.categoriesPane);
    this.add(this.scriptsPane);
    this.add(this.blockTemplatesPane);

    // Configure the panes
    this.categoriesPane.setWidth(250);
    this.categoriesPane.setHeight(140);

    this.scriptsPane.setWidth(this.width() - this.categoriesPane.width());
    this.scriptsPane.setHeight(height);
    this.scriptsPane.setLeft(this.categoriesPane.width() + 1);
    this.scriptsPane.setColor(new Color(80, 80, 101));

    this.blockTemplatesPane.setWidth(this.categoriesPane.width()); //same width than categoriesPane
    this.blockTemplatesPane.setHeight(this.height() - this.categoriesPane.height());
    this.blockTemplatesPane.setTop(this.categoriesPane.height());  //under categoriesPane   
    this.blockTemplatesPane.setColor(new Color(8, 80, 121));

    // Create the buttons for the categoriesPane, set their position and add them to the "buttons" pane
    this.controlsButton = this.createControlsButton();
    this.operatorsButton = this.createOperatorsButton();
    this.reportersButton = this.createReportersButton();

    this.controlsButton.setCenter(new Point(this.categoriesPane.width() / 3, 33));
    this.operatorsButton.setCenter(new Point(this.categoriesPane.width() * 2 / 3, 33));
    this.reportersButton.setCenter(new Point(this.categoriesPane.width() / 3, 73));

    this.categoriesPane.add(this.controlsButton);
    this.categoriesPane.add(this.operatorsButton);
    this.categoriesPane.add(this.reportersButton);

    //create panes where the block templates go, all the panes are hidden and they are shown when clicking on a button in the Categories Pane
    this.controlsTemplatesPane = this.blockTemplatesPane.children[0].copy(); //we are copying the first child which is a FrameMorph
    this.operatorsTemplatesPane = this.blockTemplatesPane.children[0].copy(); //we are copying the first child which is a FrameMorph    this.addReportersPane = this.blockTemplatesPane.children[0].copy(); //we are copying the first child which is a FrameMorph
    this.addReportersPane = this.blockTemplatesPane.children[0].copy(); //we are copying the first child which is a FrameMorph    this.addReportersPane = this.blockTemplatesPane.children[0].copy(); //we are copying the first child which is a FrameMorph

    this.controlsTemplatesPane.hide();
    this.operatorsTemplatesPane.hide();
    this.addReportersPane.hide();

    this.blockTemplatesPane.add(this.controlsTemplatesPane);
    this.blockTemplatesPane.add(this.operatorsTemplatesPane);
    this.blockTemplatesPane.add(this.addReportersPane);

    //create and add block templates to the Block Templates Pane
    this.addBlocksToControlsTemplatesPane();
    this.addBlocksToOperatorsTemplatesPane();
    this.addBlocksToAddReportersPane();

    // Create panes for the Reporters and Stages and add them to ScriptsPane
    this.stagesPane = this.createStagesPane(); // the scripts pane has to start with a pane to hold the stages (basically the scripts)vz
    this.reportersPane = this.createReportersPane(); // the scripts pane has to start with a pane to list the available: variables, feeds and attributes
    this.scriptsPane.add(this.stagesPane);
    this.scriptsPane.add(this.reportersPane);

    // Create and add morphs to hold the variables, feeds, attributes..
    this.listOfVariables = this.createListOfVariables();
    this.reportersPane.add(this.listOfVariables);
    this.listOfFeeds = this.createListOfFeeds();
    this.reportersPane.add(this.listOfFeeds);
    this.listOfAttributes = this.createListOfAttributes();
    this.reportersPane.add(this.listOfAttributes);
    //this.listOfRules = this.createListOfRules(); // Not implemented yet
    //this.reportersPane.add(this.listOfRules); // Not implemented yet

    // Create Dialog for listing nodes and feed tags
    this.listOfTagsForFeedsDialog = this.createListOfTagsForFeedsDialog();
    this.add(this.listOfTagsForFeedsDialog);
    this.listOfNodesForAttributesDialog = this.createListOfNodesForAttributesDialog();
    this.add(this.listOfNodesForAttributesDialog);

    // Create array to hold all the feeds dialogs (each feed dialog will display all the feeds for a given tag)
    // Same for attributes (display attribute by node)
    this.arrayOfFeedsDialogs = this.createArrayOfFeedsDialogs();
    for (var key in this.arrayOfFeedsDialogs) {
        // console.log(this.arrayOfFeedsDialogs[key]);
        if (this.arrayOfFeedsDialogs[key].isMorph)
            this.add(this.arrayOfFeedsDialogs[key]);
    }
    //console.log(this.arrayOfFeedsDialogs);
    /*this.arrayOfFeedsDialogs.forEach(function (dialog, index) {
     myself.add(dialog);
     console.log('myself');
     });*/
    this.arrayOfAttributesDialogs = this.createArrayOfAttributesDialogs();
    this.arrayOfAttributesDialogs.forEach(function (dialog, index) {
        myself.add(dialog);
    });

    // load blocks if we are editing a rule
    if (this.loadRule === true)
        this.loadBlocks(this.blocks_string);
    else // There is always a default variable: timedout. We create and add it if we are not loading (editing) a rule, in this case "timedout" will be loaded with the other variables
        this.addElementToList(this.listOfVariables, 'timedout');

    //button to see blocks xml and display in the console for debugging
    this.genereateCodeButton = this.createGenerateXMLButton();
};

emonCMS_RulesIDE_Morph.prototype.createControlsButton = function (width, height) {
    button = new ToggleButtonMorph(
            null, //colors,
            this, // the IDE is the target
            "showControlsTemplatesPane", //action
            "Commands", //label string
            function () {  // query
                // return something - i think this something is used in action, but i am not sure
            }
    );
    button.corner = 12;
    button.color = new Color(55, 55, 05);
    button.highlightColor = new Color(55, 55, 65);
    button.pressColor = new Color(55, 55, 75);
    button.labelMinExtent = new Point(36, 18);
    button.padding = 0;
    button.labelShadowOffset = new Point(-1, -1);
    button.labelShadowColor = new Color(55, 55, 55);
    button.labelColor = new Color(255, 255, 255);
    button.minWidth = 70;
    button.drawNew();
    button.fixLayout();
    button.refresh();
    return button;
};

emonCMS_RulesIDE_Morph.prototype.createOperatorsButton = function (width, height) {
    button = new ToggleButtonMorph(
            null, //colors,
            this, // the IDE is the target
            "showOperatorsTemplatesPane", //action
            "Operators", //label string
            function () {  // query
                // return something - i think this something is used in action, but i am not sure
            }
    );
    button.corner = 12;
    button.color = new Color(55, 55, 05);
    button.highlightColor = new Color(55, 55, 65);
    button.pressColor = new Color(55, 55, 75);
    button.labelMinExtent = new Point(36, 18);
    button.padding = 1;
    button.labelShadowOffset = new Point(-1, -1);
    button.labelShadowColor = new Color(55, 55, 55);
    button.labelColor = new Color(255, 255, 255);
    button.minWidth = 70;
    button.f
    button.fixLayout();
    button.refresh();
    return button;
};

emonCMS_RulesIDE_Morph.prototype.createReportersButton = function (width, height) {
    button = new ToggleButtonMorph(
            null, //colors,
            this, // the IDE is the target
            "showaddReportersPane", //action
            "Reporters", //label string
            function () {  // query
                // return something - i think this something is used in action, but i am not sure
            }
    );
    button.corner = 12;
    button.color = new Color(55, 55, 05);
    button.highlightColor = new Color(55, 55, 65);
    button.pressColor = new Color(55, 55, 75);
    button.labelMinExtent = new Point(36, 18);
    button.padding = 1;
    button.labelShadowOffset = new Point(-1, -1);
    button.labelShadowColor = new Color(55, 55, 55);
    button.labelColor = new Color(255, 255, 255);
    button.minWidth = 70;
    button.drawNew();
    button.fixLayout();
    button.refresh();
    return button;
};

emonCMS_RulesIDE_Morph.prototype.showControlsTemplatesPane = function () {
    this.blockTemplatesPane.children.forEach(function (value, index) {
        value.hide();
    });
    this.controlsTemplatesPane.show();
};

emonCMS_RulesIDE_Morph.prototype.showOperatorsTemplatesPane = function () {
    this.blockTemplatesPane.children.forEach(function (value, index) {
        value.hide();
    });
    this.operatorsTemplatesPane.show();
};

emonCMS_RulesIDE_Morph.prototype.showaddReportersPane = function () {
    this.blockTemplatesPane.children.forEach(function (value, index) {
        value.hide();
    });
    this.addReportersPane.show();
};

emonCMS_RulesIDE_Morph.prototype.addBlocksToControlsTemplatesPane = function () {
    var myself = this;
    var top = this.controlsTemplatesPane.top() + 10;

    list_of_hat_blocks = ['Stage']; //in this array we specify the "selector" for the each "command" block we want to add, the list of blocks can be found in "objects.js" in "SpriteMorph.prototype.initBlocks" and "emonCMS_RulesObjects"
    list_of_hat_blocks.forEach(function (value, index) {
        block = new HatBlockMorph();
        block.setSelector(value);
        block.isTemplate = true;
        block.isDraggable = false;
        block.zebraContrast = 40;
        block.setPosition(new Point(10, top));
        top = top + block.height() + 10; //calculate "top" for the next block
        myself.controlsTemplatesPane.add(block);
    });

    list_of_command_blocks = ['doIf', 'doIfElse', 'requestFeed', 'setAttribute']; //in this array we specify the "selector" for each "command" block we want to add, the list of blocks can be found in "objects.js" in "SpriteMorph.prototype.initBlocks". If you are creating a new block, add it to "emonCMS_RulesObjects"
    list_of_command_blocks.forEach(function (value, index) {
        block = new CommandBlockMorph();
        block.setSelector(value);
        block.isTemplate = true;
        block.isDraggable = false;
        block.setPosition(new Point(10, top));
        block.zebraContrast = 40;
        top = top + block.height() + 10; //calculate "top" for the next block
        myself.controlsTemplatesPane.add(block);
    });


    list_of_reporters_blocks = ['getLastFeed'/*, 'getFeedHistorical'*/];
    list_of_reporters_blocks.forEach(function (value, index) {
        block = new ReporterBlockMorph();
        block.setSelector(value);
        block.isTemplate = true;
        block.isDraggable = false;
        block.setPosition(new Point(10, top));
        block.zebraContrast = 40;
        top = top + block.height() + 10; //calculate "top" for the next block
        myself.controlsTemplatesPane.add(block);
    });

};

emonCMS_RulesIDE_Morph.prototype.addBlocksToOperatorsTemplatesPane = function () {
    var myself = this;
    var top = this.operatorsTemplatesPane.top() + 10;
    list_of_reporter_blocks = ['reportLessThan', 'reportEquals', 'reportGreaterThan']; //in this array we specify the "selector" for each block we want to add, the list of blocks can be found in "objects.js" in "SpriteMorph.prototype.initBlocks" and "emonCMS_RulesObjects". If you are creating a new block, add it to "emonCMS_RulesObjects"
    list_of_reporter_blocks.forEach(function (value, index) {
        block = new ReporterBlockMorph();
        block.setSelector(value);
        block.isTemplate = true;
        block.isDraggable = false;
        block.setPosition(new Point(10, top));
        top = top + block.height() + 10; //calculate "top" for the next block
        myself.operatorsTemplatesPane.add(block);
    });
};

emonCMS_RulesIDE_Morph.prototype.addBlocksToAddReportersPane = function () {
    // We create and add buttons:
    //   - New variable
    //   - Add feed, this will prompt the available nodes and its feeds, once the user clicks on one, the feed is added to the Reporters Pane and can be used in a Stage
    //   - Add attribute: the same than "add feed"
    var list_of_buttons = [], myself = this;
    var top = this.addReportersPane.top() + 10;

    //button "new variable"
    button = new PushButtonMorph(
            null,
            function () {
                new VariableDialogMorph(
                        null,
                        addVar,
                        myself
                        ).prompt(
                        'Variable name',
                        null,
                        myself.world()
                        );
            },
            'New variable'
            );
    //button.selector = 'addVariable';
    list_of_buttons.push(button);

    //button "add feed"
    button = new PushButtonMorph(
            null,
            function () {
                myself.listOfTagsForFeedsDialog.show();
            },
            'Add feed'
            );
    //button.selector = 'addFeed';
    list_of_buttons.push(button);

    //button "add attribute"
    button = new PushButtonMorph(
            null,
            function () {
                myself.listOfNodesForAttributesDialog.show();
            },
            'Add attribute'
            );
    //button.selector = 'addAttribute';
    list_of_buttons.push(button);

    // add blocks to the pane
    list_of_buttons.forEach(function (block, key) {
        block.setPosition(new Point(10, top));
        top = top + block.height() + 10; //calculate "top" for the next block
        myself.addReportersPane.add(block);
    });

    // Functions called by the buttons
    function addVar(pair) {
        //myself.addVariableToList(pair);
        myself.addElementToList(myself.listOfVariables, pair[0]);
    }
};

emonCMS_RulesIDE_Morph.prototype.addElementToList = function (list, name) {
    // Variables to use
    var lastChild = list.children[list.children.length - 1];
    var top, left;

    // Create reporter block and configure
    reporter = new ReporterBlockMorph();
    reporter.setSelector('reportGetVar', name);
    reporter.isTemplate = true;
    reporter.isDraggable = false;

    // Calculate position for the reporter
    if (list.children.length === 1) { // if the only child is the Title, aka this is the first reporter we are adding to the list
        left = list.left() + 10; // beginning of row
        top = lastChild.top() + 25; // Under the title
    }
    else if (lastChild.right() + reporter.width() + 20 > list.right()) { // if the reporter would reach the right edge of the list
        left = list.left() + 10;
        top = lastChild.top() + 20; // next row
    }
    else { // We add the reporter after the last child
        left = lastChild.right() + 10;
        top = lastChild.top();
    }
    reporter.setPosition(new Point(left, top));

    list.add(reporter);
    reporter.show();
    //the dimensions of the pane may have changed so we need to redraw the reposrtersPane
    this.reportersPaneFixLayout();

};

emonCMS_RulesIDE_Morph.prototype.addVariableToList = function (pair) {
    //calculate position in the scripts pane for the variable block
    var top = this.scriptsPane.top() - 20 + 15 * this.listOfVariables.children.length;
    left = this.listOfVariables.left() + 10;

    // Create variable block and configure
    variable = new ReporterBlockMorph();
    variable.setSelector('reportGetVar', pair[0]);
    variable.isTemplate = true;
    variable.isDraggable = false;
    variable.setPosition(new Point(left, top));

    //add variable
    this.listOfVariables.add(variable); // childre[1] is a MultiArgMorph
    this.variables.push(pair[0]);

    // Show the varaible
    variable.show();

    //the dimensions of the pane may have changed so we need to redraw the reposrtersPane
    this.reportersPaneFixLayout();
};

emonCMS_RulesIDE_Morph.prototype.createStagesPane = function () {
    var pane = new ScriptsMorph();
    pane.setWidth(this.scriptsPane.width() * 2 / 3);
    pane.setHeight(this.scriptsPane.height());
    pane.setPosition(new Point(this.scriptsPane.left(), 0));
    pane.setColor(new Color(80, 80, 121));
    return pane;
};

emonCMS_RulesIDE_Morph.prototype.createReportersPane = function () {
    var pane = new ScriptsMorph();
    pane.acceptsDrops = false; // this pane displays a list of available varaibles, feeds, attrinbnutes... They are added programatically, we dont want to be able to drop anything in this pane
    pane.setWidth(this.scriptsPane.width() * 1 / 3);
    pane.setHeight(this.scriptsPane.height());
    pane.setPosition(new Point(this.stagesPane.left() + this.stagesPane.width(), 0));
    pane.setColor(new Color(50, 80, 121));
    return pane;
};

emonCMS_RulesIDE_Morph.prototype.createListOfVariables = function () { // this list is really a pane
    var pane = new ScriptsMorph();
    pane.setHeight(20);
    pane.setWidth(this.reportersPane.width());
    pane.setColor(new Color(50, 60, 121));
    pane.setPosition(new Point(this.reportersPane.left() + 10, 10));
    var title = new StringMorph("Variables");
    title.setPosition(new Point(pane.left() + 10, 20));
    title.setColor(new Color(255, 255, 255, 1));
    pane.add(title);

    return pane;
};

emonCMS_RulesIDE_Morph.prototype.createListOfFeeds = function () { // this list is really a pane
    var pane = new ScriptsMorph();
    pane.setHeight(20);
    pane.setWidth(this.reportersPane.width());
    pane.setColor(new Color(50, 60, 121));
    pane.setPosition(new Point(this.reportersPane.left() + 10, 10));
    var title = new StringMorph("Feeds");
    title.setPosition(new Point(pane.left() + 10, 20));
    title.setColor(new Color(255, 255, 255, 1));
    pane.add(title);
    return pane;
};

emonCMS_RulesIDE_Morph.prototype.createListOfAttributes = function () { // this list is really a pane
    var pane = new ScriptsMorph();
    pane.setHeight(20);
    pane.setWidth(this.reportersPane.width());
    pane.setColor(new Color(50, 60, 121));
    pane.setPosition(new Point(this.reportersPane.left() + 10, 10));
    var title = new StringMorph("Attributes");
    title.setPosition(new Point(pane.left() + 10, 20));
    title.setColor(new Color(255, 255, 255, 1));
    pane.add(title);
    return pane;
};

emonCMS_RulesIDE_Morph.prototype.createListOfRules = function () { // this list is really a pane
    var pane = new ScriptsMorph();
    pane.setHeight(20);
    pane.setWidth(this.reportersPane.width());
    pane.setColor(new Color(50, 60, 121));
    pane.setPosition(new Point(this.reportersPane.left() + 10, 10));
    var title = new StringMorph("Rules");
    title.setPosition(new Point(pane.left() + 10, 20));
    title.setColor(new Color(255, 255, 255, 1));
    pane.add(title);
    return pane;
};

emonCMS_RulesIDE_Morph.prototype.reportersPaneFixLayout = function () {
    // The order of the panes in the Reporters Pane is: VariablesList, FeedsList, AttributesList and RulesList
    // variable to use
    var lastChild;
    var top = this.reportersPane.top() + 20;

    this.reportersPane.children.forEach(function (listPane) {
        /// Set height and postion of the List
        lastChild = listPane.children[listPane.children.length - 1];
        listPane.setHeight(lastChild.bottom() - listPane.top() + 10);
        //listPane.setHeight(50);
        listPane.setTop(top);

        // Calculate position of nex List
        top = listPane.bottom() + 20;
        listPane.show();
    });
};

emonCMS_RulesIDE_Morph.prototype.createListOfTagsForFeedsDialog = function () {
    var myself = this;
    // Create the dialog where we display all the tags
    var dialog = new Morph;
    dialog.setWidth(150);
    dialog.setColor(new Color(50, 60, 121));
    dialog.setCenter(this.center());
    dialog.setTop(this.top() + 100);
    dialog.isDraggable = true;
    dialog.hide();

    var title = new StringMorph('Choose a feed tag');
    title.setColor(new Color(255, 255, 255, 1));
    title.setLeft(dialog.left() + 20);
    title.setTop(dialog.top() + 10);
    dialog.add(title);

    if (title.width() > dialog.width())
        dialog.setWidth(title.width() + 20);

    //Create one button per tag and add to the dialog
    // Postion of the first button
    var top = dialog.top() + 35;
    var left = dialog.left() + 20;

    for (var key in this.array_of_feeds_by_tag) {
        var tag_key = key;
        button = new PushButtonMorph(
                function () {//function to be called when clicking the button
                    myself.showFeedsDialog(arguments[0]);
                },
                tag_key, //variable to pass to the function as "arguments[0]"
                tag_key
                );
        // Check if there is enugh space in this row of buttons to add the button
        if ((left + button.width()) < dialog.right() - 20)
            button.setPosition(new Point(left, top));
        else {
            left = dialog.left() + 20;
            top = top + 30;
            button.setPosition(new Point(left, top));
        }
        // calculate new position for next button
        left = button.right() + 10;
        dialog.add(button);
    }

    //create a cancel button and add it 
    var cancel_button = new PushButtonMorph(
            null,
            function () {
                myself.hideFeedsDialog();
            },
            'Cancel'
            );
    cancel_button.setColor(new Color(220, 220, 10));
    cancel_button.setPosition(new Point(dialog.right() - 10 - cancel_button.width(), button.bottom() + 15));
    cancel_button.fixLayout();

    dialog.add(cancel_button);

    // Set the height of the dialog according to the number of rows of buttons
    dialog.setHeight(cancel_button.bottom() - dialog.top() + 15);

    return dialog;
};

emonCMS_RulesIDE_Morph.prototype.createListOfNodesForAttributesDialog = function () {
    var myself = this;
    // Create the dialog where we display all the nodes
    var dialog = new Morph;
    dialog.setWidth(150);
    dialog.setColor(new Color(50, 60, 121));
    dialog.setCenter(this.center());
    dialog.setTop(this.top() + 100);
    dialog.isDraggable = true;
    dialog.hide();

    var title = new StringMorph('Choose a node');
    title.setColor(new Color(255, 255, 255, 1));
    title.setLeft(dialog.left() + 20);
    title.setTop(dialog.top() + 10);
    dialog.add(title);

    //Create the one button per node and add to the dialog
    // Postion of the first button
    var top = dialog.top() + 35;
    var left = dialog.left() + 20;

    for (var key in this.array_of_attributes_by_node) {
        var node_key = key;
        button = new PushButtonMorph(
                function () {//function to be called when clicking the button
                    myself.showAttributesDialog(arguments[0]);
                },
                node_key, //variable to pass to the function as "arguments[0]"
                'Node ' + node_key
                );
        // Check if there is enugh space in this row of buttons to add the button
        if ((left + button.width()) < dialog.right() - 20)
            button.setPosition(new Point(left, top));
        else {
            left = dialog.left() + 20;
            top = top + 30;
            button.setPosition(new Point(left, top));
        }
        // calculate new position for next button
        left = button.right() + 10;
        dialog.add(button);
    }

    //create a cancel button and add it 
    var cancel_button = new PushButtonMorph(
            null,
            function () {
                myself.hideAttributesDialog();
            },
            'Cancel'
            );
    cancel_button.setColor(new Color(220, 220, 10));
    cancel_button.setPosition(new Point(dialog.right() - 10 - cancel_button.width(), button.bottom() + 15));
    cancel_button.fixLayout();

    dialog.add(cancel_button);

    // Set the height of the dialog according to the number of rows of buttons
    dialog.setHeight(cancel_button.bottom() - dialog.top() + 15);

    return dialog;
};

emonCMS_RulesIDE_Morph.prototype.showFeedsDialog = function (node_key) {
    //hide all the dialogs
    this.arrayOfFeedsDialogs.forEach(function (dialog) {
        dialog.hide();
    });
    this.listOfTagsForFeedsDialog.hide();
    //show the target dialog     
    this.arrayOfFeedsDialogs[node_key].show();
    //console.log(this.arrayOfFeedsDialogs);
    //console.log(this.arrayOfFeedsDialogs[node_key]);
};

emonCMS_RulesIDE_Morph.prototype.showAttributesDialog = function (node_key) {
    //hide all the dialogs
    this.arrayOfAttributesDialogs.forEach(function (dialog) {
        dialog.hide();
    });
    this.listOfNodesForAttributesDialog.hide();
    //show the target dialog     
    this.arrayOfAttributesDialogs[node_key].show();
};

emonCMS_RulesIDE_Morph.prototype.hideFeedsDialog = function () {
    //hide all the dialogs
    for (var key in this.arrayOfFeedsDialogs) {
        if (this.arrayOfFeedsDialogs[key].isMorph)
            this.arrayOfFeedsDialogs[key].hide();
    }
    this.listOfTagsForFeedsDialog.hide();
};

emonCMS_RulesIDE_Morph.prototype.hideAttributesDialog = function () {
    //hide all the dialogs
    this.arrayOfAttributesDialogs.forEach(function (dialog) {
        dialog.hide();
    });
    this.listOfNodesForAttributesDialog.hide();
};

emonCMS_RulesIDE_Morph.prototype.createArrayOfFeedsDialogs = function () {
    var myself = this;
    var array_of_feeds_dialogs = [];

    // Create a Dialog displaying all the feeds for each tag
    // console.log(this.array_of_feeds_by_tag);
    for (var tag_key in this.array_of_feeds_by_tag) { // we create one dialog per feed tag
        var dialog = new Morph;
        dialog.setWidth(150);
        dialog.setColor(new Color(50, 60, 121));
        dialog.setCenter(this.center());
        dialog.setTop(this.top() + 100);
        dialog.isDraggable = true;
        dialog.hide();

        var title = new StringMorph('Tag ' + tag_key + ' - Choose a feed');
        title.setColor(new Color(255, 255, 255, 1));
        title.setLeft(dialog.left() + 20);
        title.setTop(dialog.top() + 10);
        dialog.add(title);
        
        if (title.width() + 20 > dialog.width()){
            dialog.setWidth(title.width() + 40);
        }

        // Postion of the first button
        var top = dialog.top() + 35;
        var left = dialog.left() + 20;
        for (var feed_key in this.array_of_feeds_by_tag[tag_key]) { // we add a button per each feed,
            var feed = this.array_of_feeds_by_tag[tag_key][feed_key];
            //console.log(feed);
            if (typeof feed == 'object') {
                var button_label = 'F' + feed['id'] + ' - ' + feed['name'];
                button = new PushButtonMorph(
                        function () {
                            myself.addElementToList(myself.listOfFeeds, arguments[0]);
                        },
                        button_label, //variable to be used in the function above, accesed as "arguments[0]"
                        button_label
                        );
                // Check if there is enugh space in this row of buttons to add the button
                if ((left + button.width()) < dialog.right() - 20)
                    button.setPosition(new Point(left, top));
                else {
                    left = dialog.left() + 20;
                    top = top + 30;
                    button.setPosition(new Point(left, top));
                }
                // calculate new position for next button
                left = button.right() + 10;
                dialog.add(button);
            }
        }
        //create a cancel button and add it 
        var cancel_button = new PushButtonMorph(null,
                function () {
                    myself.hideFeedsDialog();
                },
                'Cancel'
                );
        cancel_button.setColor(new Color(220, 220, 10));
        cancel_button.setPosition(new Point(dialog.right() - 10 - cancel_button.width(), button.bottom() + 15));
        cancel_button.fixLayout();
        dialog.add(cancel_button);

        // Set the height of the dialog according to the number of rows of buttons
        dialog.setHeight(cancel_button.bottom() - dialog.top() + 15);
        array_of_feeds_dialogs[tag_key] = dialog;
    }
    //console.log(array_of_feeds_dialogs);
    return array_of_feeds_dialogs;
};

emonCMS_RulesIDE_Morph.prototype.createArrayOfAttributesDialogs = function () {
    var myself = this;
    var array_of_attributes_dialogs = [];

    // Create a Dialog displaying all the feeds for each node
    for (var node_key in this.array_of_attributes_by_node) {
        var dialog = new Morph;
        dialog.setWidth(150);
        dialog.setColor(new Color(50, 60, 121));
        dialog.setCenter(this.center());
        dialog.setTop(this.top() + 100);
        dialog.isDraggable = true;
        dialog.hide();

        var title = new StringMorph('Node ' + node_key + ': choose an attribute');
        title.setColor(new Color(255, 255, 255, 1));
        title.setLeft(dialog.left() + 20);
        title.setTop(dialog.top() + 10);
        dialog.add(title);

        // Postion of the first button
        var top = dialog.top() + 35;
        var left = dialog.left() + 20;

        for (var attribute_key in this.array_of_attributes_by_node[node_key]) {
            var attribute = this.array_of_attributes_by_node[node_key][attribute_key];
            button_label = 'A' + attribute['attributeUid'] + ' - ' + attribute['groupid'] + attribute['attributeId'] + attribute['attributeNumber'] + attribute['nodeid'];
            button = new PushButtonMorph(
                    function () {
                        myself.addElementToList(myself.listOfAttributes, arguments[0]);
                    },
                    button_label, //variable to be used in the function above, accesed as "arguments[0]"
                    button_label
                    );
            // Check if there is enugh space in this row of buttons to add the button
            if ((left + button.width()) < dialog.right() - 20)
                button.setPosition(new Point(left, top));
            else {
                left = dialog.left() + 20;
                top = top + 30;
                button.setPosition(new Point(left, top));
            }
            // calculate new position for next button
            left = button.right() + 10;
            dialog.add(button);
        }
        //create a cancel button and add it 
        var cancel_button = new PushButtonMorph(null,
                function () {
                    myself.hideAttributesDialog();
                },
                'Cancel'
                );
        cancel_button.setColor(new Color(220, 220, 10));
        cancel_button.setPosition(new Point(dialog.right() - 10 - cancel_button.width(), button.bottom() + 15));
        cancel_button.fixLayout();
        dialog.add(cancel_button);



        // Set the height of the dialog according to the number of rows of buttons
        dialog.setHeight(cancel_button.bottom() - dialog.top() + 15);
        array_of_attributes_dialogs[node_key] = dialog;
        //console.log(array_of_attributes_dialogs);
    }

    return array_of_attributes_dialogs;
};

emonCMS_RulesIDE_Morph.prototype.createGenerateXMLButton = function () {
    var myself = this;
    var button = new PushButtonMorph(null,
            function () {
                myself.generateXML();
            },
            'Generate XML code'
            );
    button.setColor(new Color(220, 220, 10));
    button.setLeft(this.scriptsPane.left() + 50);
    button.setTop(this.scriptsPane.top() + 20);
    button.fixLayout();
    this.add(button);
};

emonCMS_RulesIDE_Morph.prototype.generateXML = function () {
    var serializer = new XML_Serializer();
    var xml_string = '<blocks>' + '<stages>' + serializer.serialize(this.stagesPane) + '</stages>' +
            '<variables>' + serializer.serialize(this.listOfVariables) + '</variables>' +
            '<feeds>' + serializer.serialize(this.listOfFeeds) + '</feeds>' +
            '<attributes>' + serializer.serialize(this.listOfAttributes) + '</attributes>' + '</blocks>';
    console.log(xml_string);
    return xml_string;
};

emonCMS_RulesIDE_Morph.prototype.loadBlocks = function (blocks_string) {
    //var serializer = new XML_Serializer();
    var serializer = new SnapSerializer();
    var myself = this;

    var stages_string = getBlocksString(blocks_string, "stages");
    var variables_string = getBlocksString(blocks_string, "variables");
    var feeds_string = getBlocksString(blocks_string, "feeds");
    var attributes_string = getBlocksString(blocks_string, "attributes");

    // Load Reporters
    loadReporters(this.listOfAttributes, attributes_string);
    loadReporters(this.listOfFeeds, feeds_string);
    loadReporters(this.listOfVariables, variables_string);

    // Load Stages
    loadStages(stages_string, this.stagesPane);
    //console.log(stages_string);


    // Local functions
    function getBlocksString(blocks_string, script_name) {
        var begining_of_slice = blocks_string.search('<' + script_name + '>');
        var end_of_slice = blocks_string.search('</' + script_name + '>') + script_name.length + 3;
        return blocks_string.slice(begining_of_slice, end_of_slice); // returns something like: <attributes><script x="10" y="35"><block var="1212120"/></script><script x="72" y="35"><block var="0x06500x06500x065100"/></script></attributes>
    }
    function loadReporters(list, blocks_string) {
        var blocks_XML_Element = serializer.parse(blocks_string);
        blocks_XML_Element.children.forEach(function (child) { //each child is a <script>
            reporter_name = child.children[0].attributes.var;
            myself.addElementToList(list, reporter_name);
        });
    }
    function loadStages(stages_string, stagesPane) {
        //console.log(stages_string);
        var stages_XML_Element = serializer.parse(stages_string);
        var scripts = stagesPane;
        stages_XML_Element.children.forEach(function (child) { //each child is a <script> whose children are a list of blocks (starting with a Stage)
            //console.log(child);
            //console.log(child.children[0]);
            element = serializer.loadScript(child);
            if (!element) {
                return;
            }
            element.setPosition(new Point(
                    (+child.attributes.x || 0),
                    (+child.attributes.y || 0)
                    ).add(scripts.topLeft()));
            scripts.add(element);
            element.fixBlockColor(null, true); // force zebra coloring
        });
    }

};



/*
 * 
 * Table contstructors
 
 CREATE TABLE `Node_reg` (
 `NodeID` int(11) NOT NULL,
 `FromAddress` varchar(255) DEFAULT NULL,
 `MACAddress` text NOT NULL,
 PRIMARY KEY (`NodeID`)
 ) ENGINE=InnoDB DEFAULT CHARSET=latin1
 
 CREATE TABLE `attributes` (
 `attributeUid` int(11) NOT NULL AUTO_INCREMENT,  `nodeid` int(11) NOT NULL,
 `groupid` varchar(255) NOT NULL,
 `attributeId` varchar(255) NOT NULL,
 `attributeNumber` varchar(255) NOT NULL,
 `attributeDefaultValue` varchar(255) NOT NULL,
 `inputId` varchar(255) NOT NULL DEFAULT '0',
 `feedId` varchar(256) NOT NULL DEFAULT '0',
 PRIMARY KEY (`attributeUid`),
 UNIQUE KEY `attributes` (`attributeUid`),
 KEY `nodeid` (`nodeid`),
 KEY `attributeUid` (`attributeUid`,`nodeid`,`groupid`,`attributeId`,`attributeNumber`,`attributeDefaultValue`,`inputId`,`feedId`)
 ) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1
 
 */
 