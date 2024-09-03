ace.require("ace/ext/language_tools");

function logErrorToConsole(errorMessage) {
    console.error("SQL Execution Error: " + errorMessage);
}

var editor = ace.edit("editor");
editor.session.setMode("ace/mode/sql");
editor.setOptions({
    enableBasicAutocompletion: true, // Enable basic autocompletion
    enableLiveAutocompletion: true,  // Enable live autocompletion
    enableSnippets: true             // Enable code snippets
});

var customCompleter = {
    getCompletions: function(editor, session, pos, prefix, callback) {
        var suggestions = [];

        for (var table in schema) {
            if (schema.hasOwnProperty(table)) {
                suggestions.push({
                    caption: table,
                    value: table,
                    meta: "table"
                });

                schema[table].forEach(function(column) {
                    suggestions.push({
                        caption: table + "." + column,
                        value: table + "." + column,
                        meta: "column"
                    });
                });
            }
        }

        callback(null, suggestions);
    }
};

editor.completers = [customCompleter,
    ace.require("ace/ext/language_tools").keyWordCompleter,
    ace.require("ace/ext/language_tools").textCompleter,
    ace.require("ace/ext/language_tools").snippetCompleter
];

// Load the last query if it exists
if (lastQuery) {
    editor.setValue(lastQuery, 1);
}

// Sync Ace Editor content with the hidden input field
function syncEditorContent() {
    document.getElementById("query").value = editor.getValue();
}

var form = document.querySelector("form");
form.addEventListener("submit", function() {
    syncEditorContent();
});

// Add event listener for Command+Enter
editor.commands.addCommand({
    name: "executeQuery",
    bindKey: {win: "Ctrl-Enter", mac: "Command-Enter"},
    exec: function() {
        syncEditorContent();  // Ensure the query field is updated
        form.submit();
    }
});