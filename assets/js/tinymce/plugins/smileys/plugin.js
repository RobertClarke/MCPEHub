tinymce.PluginManager.add('smileys', function (editor, url) {
    var defaultSmileys = [
        [
			{ url: 'cool', title: '' },
			{ url: 'happy1', title: '' },
			{ url: 'happy2', title: '' },
			{ url: 'happy3', title: '' },
			{ url: 'happy4', title: '' },
			{ url: 'wink', title: '' },
			{ url: 'sad', title: '' },
			{ url: 'crying', title: '' },
			{ url: 'notimpressed', title: '' },
			{ url: 'omg', title: '' },
		],
		[
			{ url: 'woah', title: '' },
			{ url: 'tongue', title: '' },
			{ url: 'sweating', title: '' },
			{ url: 'angel', title: '' },
			{ url: 'inlove', title: '' },
			{ url: 'heart', title: '' },
			{ url: 'unknown', title: '' },
			{ url: 'workbench', title: '' },
			{ url: 'furnace', title: '' },
			{ url: 'coal', title: '' },
		],
		[
			{ url: 'grass', title: '' },
			{ url: 'dirt', title: '' },
			{ url: 'cobblestone', title: '' },
			{ url: 'stone', title: '' },
			{ url: 'diamond', title: '' },
			{ url: 'diamondpickaxe', title: '' },
			{ url: 'diamondshovel', title: '' },
			{ url: 'diamondaxe', title: '' },
			{ url: 'diamondsword', title: '' },
			{ url: 'gold', title: '' },
		],
		[
			{ url: 'wood', title: '' },
			{ url: 'woodslab', title: '' },
			{ url: 'sand', title: '' },
			{ url: 'nether', title: '' },
			{ url: 'iron', title: '' },
			{ url: 'ironpickaxe', title: '' },
			{ url: 'ironshovel', title: '' },
			{ url: 'ironaxe', title: '' },
			{ url: 'ironsword', title: '' },
			{ url: 'goldapple', title: '' },
		],
		[
			{ url: 'chicken', title: '' },
			{ url: 'steak', title: '' },
			{ url: 'pork', title: '' },
			{ url: 'fish', title: '' },
			{ url: 'bread', title: '' },
			{ url: 'cake', title: '' },
			{ url: 'cookie', title: '' },
			{ url: 'wheat', title: '' },
			{ url: 'melon', title: '' },
			{ url: 'apple', title: '' },
		],
		[
			{ url: 'bucket', title: '' },
			{ url: 'bucketwater', title: '' },
			{ url: 'bucketlava', title: '' },
			{ url: 'bucketmilk', title: '' },
			{ url: 'waterbottle', title: '' },
			{ url: 'pumpkin', title: '' },
			{ url: 'sugar', title: '' },
			{ url: 'sugarcane', title: '' },
			{ url: 'paper', title: '' },
			{ url: 'painting', title: '' },
		],
		[
			{ url: 'musicdisk1', title: '' },
			{ url: 'musicdisk2', title: '' },
			{ url: 'musicdisk3', title: '' },
			{ url: 'musicdisk4', title: '' },
			{ url: 'musicdisk5', title: '' },
			{ url: 'chest', title: '' },
			{ url: 'tnt', title: '' },
			{ url: 'compass', title: '' },
			{ url: 'map', title: '' },
			{ url: 'sign', title: '' },
		],
		[
			{ url: 'minecart', title: '' },
			{ url: 'boat', title: '' },
			{ url: 'fishingrod', title: '' },
			{ url: 'bed', title: '' },
			{ url: 'flint', title: '' },
			{ url: 'lapislazuli', title: '' },
			{ url: 'redstone', title: '' },
			{ url: 'slime', title: '' },
			{ url: 'snow', title: '' },
			{ url: 'bone', title: '' },
		]
    ];

    var smileys = editor.settings.smileys || defaultSmileys, fullSmileysList = editor.settings.extended_smileys ? smileys.concat(editor.settings.extended_smileys) : smileys;

    function getHtml() {
        var smileysHtml;

        smileysHtml = '<table role="presentation" class="mce-grid">';

        tinymce.each(fullSmileysList, function (row) {
            smileysHtml += '<tr>';

            tinymce.each(row, function (icon) {
                //smileysHtml += '<td><a href="#" data-mce-url="' + icon.url + '" tabindex="-1" title="' + icon.title + '"><img src="' +
                //    icon.url + '" style="width: 16px; height: 16px"></a></td>';

                smileysHtml += '<td><a href="#" data-emoticon="'+ icon.url +'"><span class="emoticon '+ icon.url +'"></span></a></td>';


            });

            smileysHtml += '</tr>';
        });

        smileysHtml += '</table>';

        return smileysHtml;
    }

    function concatArray(array) {
        var each = tinymce.each, result = [];
        each(array, function (item) {
            result = result.concat(item);
        });
        return result.length > 0 ? result : array;
    }

    function findAndReplaceDOMText(regex, node, replacementNode, captureGroup, schema) {
        var m, matches = [], text, count = 0, doc;
        var blockElementsMap, hiddenTextElementsMap, shortEndedElementsMap;

        doc = node.ownerDocument;
        blockElementsMap = schema.getBlockElements(); // H1-H6, P, TD etc
        hiddenTextElementsMap = schema.getWhiteSpaceElements(); // TEXTAREA, PRE, STYLE, SCRIPT
        shortEndedElementsMap = schema.getShortEndedElements(); // BR, IMG, INPUT

        function getMatchIndexes(m, captureGroup) {
            captureGroup = captureGroup || 0;

            var index = m.index;

            if (captureGroup > 0) {
                var cg = m[captureGroup];
                index += m[0].indexOf(cg);
                m[0] = cg;
            }

            return [index, index + m[0].length, [m[0]]];
        }

        function getText(node) {
            var txt;

            if (node.nodeType === 3) {
                return node.data;
            }

            if (hiddenTextElementsMap[node.nodeName] && !blockElementsMap[node.nodeName]) {
                return '';
            }

            txt = '';

            if (blockElementsMap[node.nodeName] || shortEndedElementsMap[node.nodeName]) {
                txt += '\n';
            }

            if ((node = node.firstChild)) {
                do {
                    txt += getText(node);
                } while ((node = node.nextSibling));
            }

            return txt;
        }

        function stepThroughMatches(node, matches, replaceFn) {
            var startNode, endNode, startNodeIndex,
                endNodeIndex, innerNodes = [], atIndex = 0, curNode = node,
                matchLocation = matches.shift(), matchIndex = 0;

            out: while (true) {
                if (blockElementsMap[curNode.nodeName] || shortEndedElementsMap[curNode.nodeName]) {
                    atIndex++;
                }

                if (curNode.nodeType === 3) {
                    if (!endNode && curNode.length + atIndex >= matchLocation[1]) {
                        // We've found the ending
                        endNode = curNode;
                        endNodeIndex = matchLocation[1] - atIndex;
                    } else if (startNode) {
                        // Intersecting node
                        innerNodes.push(curNode);
                    }

                    if (!startNode && curNode.length + atIndex > matchLocation[0]) {
                        // We've found the match start
                        startNode = curNode;
                        startNodeIndex = matchLocation[0] - atIndex;
                    }

                    atIndex += curNode.length;
                }

                if (startNode && endNode) {
                    curNode = replaceFn({
                        startNode: startNode,
                        startNodeIndex: startNodeIndex,
                        endNode: endNode,
                        endNodeIndex: endNodeIndex,
                        innerNodes: innerNodes,
                        match: matchLocation[2],
                        matchIndex: matchIndex
                    });

                    // replaceFn has to return the node that replaced the endNode
                    // and then we step back so we can continue from the end of the
                    // match:
                    atIndex -= (endNode.length - endNodeIndex);
                    startNode = null;
                    endNode = null;
                    innerNodes = [];
                    matchLocation = matches.shift();
                    matchIndex++;

                    if (!matchLocation) {
                        break; // no more matches
                    }
                } else if ((!hiddenTextElementsMap[curNode.nodeName] || blockElementsMap[curNode.nodeName]) && curNode.firstChild) {
                    // Move down
                    curNode = curNode.firstChild;
                    continue;
                } else if (curNode.nextSibling) {
                    // Move forward:
                    curNode = curNode.nextSibling;
                    continue;
                }

                // Move forward or up:
                while (true) {
                    if (curNode.nextSibling) {
                        curNode = curNode.nextSibling;
                        break;
                    } else if (curNode.parentNode !== node) {
                        curNode = curNode.parentNode;
                    } else {
                        break out;
                    }
                }
            }
        }

        /**
        * Generates the actual replaceFn which splits up text nodes
        * and inserts the replacement element.
        */
        function genReplacer(nodeName) {
            var makeReplacementNode;

            if (typeof nodeName != 'function') {
                var stencilNode = nodeName.nodeType ? nodeName : doc.createElement(nodeName);

                makeReplacementNode = function () {
                    var clone = stencilNode.cloneNode(false);
                    return clone;
                };
            } else {
                makeReplacementNode = nodeName;
            }

            return function replace(range) {
                var before, after, parentNode, startNode = range.startNode,
                    endNode = range.endNode;

                if (startNode === endNode) {
                    var node = startNode;

                    parentNode = node.parentNode;
                    if (range.startNodeIndex > 0) {
                        // Add `before` text node (before the match)
                        before = doc.createTextNode(node.data.substring(0, range.startNodeIndex));
                        parentNode.insertBefore(before, node);
                    }

                    // Create the replacement node:
                    var el = makeReplacementNode();
                    parentNode.insertBefore(el, node);
                    if (range.endNodeIndex < node.length) {
                        // Add `after` text node (after the match)
                        after = doc.createTextNode(node.data.substring(range.endNodeIndex));
                        parentNode.insertBefore(after, node);
                    }

                    node.parentNode.removeChild(node);

                    return el;
                }
            };
        }

        text = getText(node);
        if (!text) {
            return;
        }
        while ((m = regex.exec(text))) {
            matches.push(getMatchIndexes(m, captureGroup));
        }

        if (matches.length) {
            count = matches.length;
            stepThroughMatches(node, matches, genReplacer(replacementNode));
        }

        return count;
    }

    function replaceAllMatches(smiley) {
        var each = tinymce.each, node = editor.selection.getNode(), marker, text;
        if (typeof (smiley.shortcut) === 'string') {
            text = smiley.shortcut.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");

            marker = editor.dom.create('img', { "src": smiley.url, "title": smiley.title });

            return findAndReplaceDOMText(new RegExp(text, 'gi'), node, marker, false, editor.schema);
        }
        else if (Array.isArray(smiley.shortcut)) {
            each(smiley.shortcut, function(item) {

                marker = editor.dom.create('img', { "src": smiley.url, "title": smiley.title });

                return findAndReplaceDOMText(new RegExp(item.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&"), 'gi'), node, marker, false, editor.schema);
            });
        }
    }

    editor.on("keyup", function (e) {
        if (!!editor.settings.auto_convert_smileys) {
            var each = tinymce.each, selection = editor.selection, node = selection.getNode();
            if (node) {
                each(concatArray(fullSmileysList), function (smiley) {
                    replaceAllMatches(smiley);
                });
            }
        }
    });

    editor.addButton('smileys', {
        type: 'panelbutton',
        icon: 'emoticons',
        panel: {
            autohide: true,
            html: getHtml,
            onclick: function (e) {
                var linkElm = editor.dom.getParent(e.target, 'a');

                if (linkElm) {

                    editor.insertContent('<span class="emoticon '+ linkElm.getAttribute('data-emoticon') +' mceNonEditable" data-mce-contenteditable="false">&nbsp;</span>');
                    //editor.insertContent('<img src="' + linkElm.getAttribute('data-mce-url') + '" title="' + linkElm.getAttribute('title') + '" />');
                    this.hide();
                }
            }
        },
        tooltip: 'Smileys'
    });
});
