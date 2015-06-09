

// The reason is to allow the context menu to appear when right clicking on a ScriptMorph
// Chagelog: 
//    - replace "IDE_Morph" with "emonCMS_RulesIDE_Morph"
//    - comment the lines where the stage variable is
//
ScriptsMorph.prototype.userMenu = function () {
    var menu = new MenuMorph(this),
            ide = this.parentThatIsA(emonCMS_RulesIDE_Morph),
            blockEditor,
            myself = this,
            obj = this.owner;
    //stage = obj.parentThatIsA(StageMorph);

    if (!ide) {
        blockEditor = this.parentThatIsA(BlockEditorMorph);
        if (blockEditor) {
            ide = blockEditor.target.parentThatIsA(emonCMS_RulesIDE_Morph);
        }
    }
    menu.addItem('clean up', 'cleanUp', 'arrange scripts\nvertically');
    menu.addItem('add comment', 'addComment');
    if (this.lastDroppedBlock) {
        menu.addItem(
                'undrop',
                'undrop',
                'undo the last\nblock drop\nin this pane'
                );
    }
    menu.addItem(
            'scripts pic...',
            'exportScriptsPicture',
            'open a new window\nwith a picture of all scripts'
            );
    if (ide) {
        menu.addLine();
        menu.addItem(
                'make a block...',
                function () {
                    new InputSlotDialogMorph(
                            null,
                            function (definition) {
                                if (definition.spec !== '') {
                                    if (definition.isGlobal) {
                                        //stage.globalBlocks.push(definition);
                                    } else {
                                        obj.customBlocks.push(definition);
                                    }
                                    ide.flushPaletteCache();
                                    ide.refreshPalette();
                                    new BlockEditorMorph(definition, obj).popUp();
                                }
                            },
                            myself
                            ).prompt(
                            'Make a block',
                            null,
                            myself.world()
                            );
                }
        );
    }
    return menu;
};

BlockMorph.prototype.setSelector = function (aSelector,variableName) {
    // private - used only for relabel()
    var oldInputs = this.inputs(),
            info;
    info = SpriteMorph.prototype.blocks[aSelector];
    this.setCategory(info.category);
    this.selector = aSelector;
    if (aSelector == 'reportGetVar') {
        this.setSpec(variableName);
    }
    else
        this.setSpec(localize(info.spec));
    this.restoreInputs(oldInputs);
    this.fixLabelColor();
};