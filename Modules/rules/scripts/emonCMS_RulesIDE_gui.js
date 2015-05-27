var emonCMS_RulesIDE_Morph;

// emonCMS_RulesIDE_Morph ///////////////////////////////////////////////////////////

// I am emonCMS_Rules top-level frame, the Editor window

// emonCMS_RulesIDE_Morph inherits from Morph:

emonCMS_RulesIDE_Morph.prototype = new Morph();
emonCMS_RulesIDE_Morph.prototype.constructor = emonCMS_RulesIDE_Morph;
emonCMS_RulesIDE_Morph.uber = Morph.prototype;

// IDE_Morph instance creation:

function emonCMS_RulesIDE_Morph(width, height, array_of_feeds_by_node) {
    this.init(width, height, array_of_feeds_by_node);
}

emonCMS_RulesIDE_Morph.prototype.init = function (width, height, array_of_feeds_by_node) {
    var myself = this;

    // initialize setting and properties
    MorphicPreferences.globalFontFamily = 'Helvetica, Arial';
    this.color = new Color(0, 32, 5); //not working
    emonCMS_RulesIDE_Morph.uber.init.call(this); // initialize inherited properties
    this.setWidth(width);
    this.setHeight(height);
    this.array_of_feeds_by_node = array_of_feeds_by_node;
    console.log(array_of_feeds_by_node);
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

    // variable to keep track of all the "variables" blocks created
    this.variables = [];

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

    // Create Dailog for listing nodes
    this.listOfNodesDialog = this.createListOfNodesDialog();
    this.add(this.listOfNodesDialog);

};


emonCMS_RulesIDE_Morph.prototype.createControlsButton = function (width, height) {
    button = new ToggleButtonMorph(
            null, //colors,
            this, // the IDE is the target
            "showControlsTemplatesPane", //action
            "Controls", //label string
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

    list_of_hat_blocks = ['Stage']; //in this array we specify the "selector" for the each "command" block we want to add, the list of blocks can be found in "objects.js" in "SpriteMorph.prototype.initBlocks"
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

    list_of_command_blocks = ['doIf', 'doIfElse', 'getLastFeed', 'requestFeed', 'setAttribute'/*, 'getFeedHistorical'*/]; //in this array we specify the "selector" for the each "command" block we want to add, the list of blocks can be found in "objects.js" in "SpriteMorph.prototype.initBlocks"
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

};

emonCMS_RulesIDE_Morph.prototype.addBlocksToOperatorsTemplatesPane = function () {
    var myself = this;
    var top = this.operatorsTemplatesPane.top() + 10;
    list_of_reporter_blocks = ['reportLessThan', 'reportEquals', 'reportGreaterThan']; //in this array we specify the "selector" for the each block we want to add, the list of blocks can be found in "objects.js" in "SpriteMorph.prototype.initBlocks"
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
            showNodes,
            'Add feed'
            );
    //button.selector = 'addFeed';
    list_of_buttons.push(button);

    // add blocks to the pane
    list_of_buttons.forEach(function (block, key) {
        block.setPosition(new Point(10, top));
        top = top + block.height() + 10; //calculate "top" for the next block
        myself.addReportersPane.add(block);
    });

    // Functions called by the buttons
    function addVar(pair) {
        myself.addVariableToList(pair);
    }
    function showNodes() {
        myself.listOfNodesDialog.show();
    }
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
    var pane = new ScrollFrameMorph();
    pane.setHeight(this.reportersPane.height() - 20);
    pane.setWidth(this.reportersPane.width() / 4);
    pane.setColor(new Color(50, 60, 121));
    pane.setPosition(new Point(this.reportersPane.left() + 10, 10));
    var title = new TextSlotMorph("Variables");
    title.setPosition(new Point(this.reportersPane.left() + 15, 20));
    title.isReadOnly = true;
    pane.add(title);
    return pane;
};

emonCMS_RulesIDE_Morph.prototype.createListOfNodesDialog = function () {
    var dialog = new Morph;
    dialog.setWidth(150);
    dialog.setColor(new Color(50, 60, 121));
    dialog.setCenter(this.center());
    dialog.isDraggable = true;
    //dialog.hide();

    var title = new StringMorph('Choose a node');
    title.setColor(new Color(255, 255, 255, 1));
    title.setLeft(dialog.left()+20);
    title.setTop(dialog.top() + 10);
    dialog.add(title);
 
    // Postion of the first button
    var top = dialog.top() + 35;
    var left = dialog.left() + 20;

    for (var node_key in this.array_of_feeds_by_node) {
        button = new PushButtonMorph(
                null,
                null, //function to be called when clicking the button
                'Node ' + node_key
                )
        // Check if there is enugh space in this row of buttons to add the button
        if ((left + button.width()) < dialog.right() - 20)
            button.setPosition(new Point(left, top))
        else {
            left = dialog.left() + 20;
            top = top + 30;
            button.setPosition(new Point(left, top))
        }
        // calculate new position for next button
        left = button.right() + 10;
        dialog.add(button);

        // Set the height of the dialof according to the number of rows of buttons
        dialog.setHeight(button.bottom() - dialog.top() +25);
        console.log(button);
        /*for (var feed_key in this.array_of_feeds_by_node[node_key]['feeds']) {
         var feed = this.array_of_feeds_by_node[node_key]['feeds'][feed_key];
         if (feed['tag'] != null && feed['tag'] != '') {
         var button_label = 'N' + node_key + 'F' + feed['feedid'] + ' - ' + feed['tag'];
         }
         else {
         var button_label = 'N' + node_key + 'F' + feed['feedid'] + ' - ' + feed['name'];
         }
         button = new PushButtonMorph(
         null,
         null, //function to be called when clicking the button
         'Node' + node_key
         )
         button.setPosition(new Point(left, top))
         // calculate new positionn for next button
         left = button.right() + 10;
         dialog.add(button);
         console.log(button);
         }
         ;*/

    }
    ;


    return dialog;
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
 `attributeUid` int(11) NOT NULL AUTO_INCREMENT,
 `nodeid` int(11) NOT NULL,
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
 