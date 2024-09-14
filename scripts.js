// Ensure Ace Editor loads only after the page is fully loaded
window.onload = function() {
    // Load Ace Editor's language tools
    ace.require("ace/ext/language_tools");

    // Log SQL errors to console
    function logErrorToConsole(errorMessage) {
        console.error("SQL Execution Error: " + errorMessage);
    }

    // Initialize Ace Editor
    var editor = ace.edit("editor");
    editor.session.setMode("ace/mode/sql");
    editor.setOptions({
        enableBasicAutocompletion: true,
        enableLiveAutocompletion: true,
        enableSnippets: true
    });

    // Define custom completer for SQL schema
    var customCompleter = {
        getCompletions: function(editor, session, pos, prefix, callback) {
            var suggestions = [];

            if (typeof schema !== 'undefined') {
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
            }

            callback(null, suggestions);
        }
    };

    // Add the custom completer to Ace Editor
    editor.completers = [customCompleter,
        ace.require("ace/ext/language_tools").keyWordCompleter,
        ace.require("ace/ext/language_tools").textCompleter,
        ace.require("ace/ext/language_tools").snippetCompleter
    ];

    // Load the last query if it exists
    if (typeof lastQuery !== 'undefined' && lastQuery) {
        editor.setValue(lastQuery, 1);  // '1' moves the cursor to the end of the query
    }

    // Function to remove SQL comments from the query
    function removeSQLComments(query) {
        // Remove single-line comments (--)
        query = query.replace(/--.*$/gm, '');
        // Remove multi-line comments (/* */)
        query = query.replace(/\/\*[\s\S]*?\*\//g, '');
        return query.trim();  // Trim the result to remove extra spaces or empty lines
    }

    // Function to sync Ace Editor content with hidden input field
    function syncEditorContent() {
        var query = editor.getValue();
        var cleanQuery = removeSQLComments(query);  // Remove comments before validation
        document.getElementById("query").value = query;  // Store the full query including comments

        // Prevent form submission if the query (after comment removal) is empty
        if (!cleanQuery.trim()) {
            alert('Query is empty. Please enter a query.');
            return false;
        }
        return true;
    }

    // Sync Ace Editor content with the hidden input field on form submission
    var form = document.querySelector("form");
    form.addEventListener("submit", function(event) {
        if (!syncEditorContent()) {
            event.preventDefault();  // Stop form submission if query is empty
        }
    });

    // Add event listener for Command+Enter to submit the form
    editor.commands.addCommand({
        name: "executeQuery",
        bindKey: {win: "Ctrl-Enter", mac: "Command-Enter"},
        exec: function() {
            if (syncEditorContent()) {
                form.submit();
            }
        }
    });
};