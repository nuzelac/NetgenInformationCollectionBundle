YUI.add('netgen-icplugin', function (Y) {

    // Good practices:
    // * use a custom namespace. 'eZConf' is used as an example here.
    // * put the plugins in a 'Plugin' sub namespace
    Y.namespace('Netgen.Plugin');

    Y.Netgen.Plugin.IcPlugin = Y.Base.create('NetgenIcPlugin', Y.Plugin.Base, [], {
        initializer: function () {
            var app = this.get('host'); // the plugged object is called host

            console.log("Hey, I'm a plugin for PlatformUI App!");
            console.log("And I'm plugged in ", app);

            console.log('Registering the ezconfListView in the app');
            app.views.netgenIcListView = {
                type: Y.Netgen.ListView,
            };

            app.route({
                name: "NetgenInformationCollectionList",
                path: "/netgen/informatin-collection/list",
                view: "netgenIcListView", // let's display the dashboard since we don't have a custom view... yet :)
                // we want the navigationHub (top menu) but not the discoveryBar
                // (left bar), we can try different options
                sideViews: {'navigationHub': true, 'discoveryBar': false},
                callbacks: ['open', 'checkUser', 'handleSideViews', 'handleMainView'],
            });
        },
    }, {
        NS: 'netgenIcTypeApp' // don't forget that
    });

    // registering the plugin for the app
    // with that, the plugin is automatically instantiated and plugged in
    // 'platformuiApp' component.
    Y.eZ.PluginRegistry.registerPlugin(
        Y.Netgen.Plugin.IcPlugin, ['platformuiApp']
    );

});
