YUI.add('netgen-listview', function (Y) {
    Y.namespace('Netgen');

    Y.Netgen.ListView = Y.Base.create('netgenIcListView', Y.eZ.TemplateBasedView, [], {
        initializer: function () {
            console.log("Hey, I'm the list view");
        },

        render: function () {
            // this.get('container') is an auto generated <div>
            // here, it's not yet in the DOM of the page and it will be added
            // after the execution of render().

            this.get('container').setHTML(
                this.template({
                    "name": "listView"
                })
            );

            return this;
        },
    });
});
