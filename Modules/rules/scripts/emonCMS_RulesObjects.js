SpriteMorph.prototype.originalInitBlocks = SpriteMorph.prototype.initBlocks;
SpriteMorph.prototype.initBlocks = function () {

    var myself = this;
    this.originalInitBlocks();

    // control
    this.blocks.Stage =
            {
                type: 'hat',
                spec: 'Stage %n',
                category: 'control'
            };
    this.blocks.getLastFeed =
            {
                type: 'command',
                spec: 'get last feed from %s',
                category: 'control'
            };
    this.blocks.requestFeed =
            {
                type: 'command',
                spec: 'request new feed from %s',
                category: 'control'
            };
    this.blocks.setAttribute =
            {
                type: 'command',
                spec: 'set attribute %s to %s',
                category: 'control'
            };
    this.blocks.getFeedHistorical =
            {
                type: 'command',
                spec: 'get last %n feeds from %s',
                category: 'control'
            };
    this.blocks.reportGetVar =
            {
                type: 'reporter',
                spec: 'get last %n feeds from %s',
                category: 'variables'
            };
    this.blocks.listOfVariables =
            {
                type: 'reporter',
                spec: 'Variables %mult%n',
                category: 'variables'
            };
};

SpriteMorph.prototype.initBlocks();
