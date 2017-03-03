YUI.add('netgen-icnavigationplugin', function (Y) {
    Y.namespace('Netgen.Plugin');

    // view service plugins must extend Y.eZ.Plugin.ViewServiceBase
    // Y.eZ.Plugin.ViewServiceBase provides several method allowing to deeply
    // hook into the view service behaviour
    Y.Netgen.Plugin.IcNavigationPlugin = Y.Base.create('netgenIcNavigationPlugin', Y.eZ.Plugin.ViewServiceBase, [], {
        initializer: function () {
            var service = this.get('host');

            console.log("Hey, I'm a plugin for NavigationHubViewService");
            console.log("And I'm plugged in ", service);

            console.log("Let's add the navigation item in the Content zone");
            service.addNavigationItem({
                Constructor: Y.eZ.NavigationItemView,
                config: {
                    title: "Collected information",
                    identifier: "netgen-information-collection-list",
                    route: {
                        name: "NetgenInformationCollectionList" // same route name of the one added in the app plugin
                    }
                }
            }, 'admin');
        },
    }, {
        NS: 'netgenIcNavigation'
    });



    Y.eZ.PluginRegistry.registerPlugin(
        Y.Netgen.Plugin.IcNavigationPlugin, ['navigationHubViewService']
    );
});
