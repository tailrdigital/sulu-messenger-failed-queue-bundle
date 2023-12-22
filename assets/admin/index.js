import {listItemActionRegistry, listToolbarActionRegistry} from 'sulu-admin-bundle/views';
import {initializer} from 'sulu-admin-bundle/services';

import ViewRetryMessageAction from "./components/ViewRetryMessageAction";
import BulkReQueueToolbarAction from "./components/BulkRequeueToolbarAction";

initializer.addUpdateConfigHook('sulu_admin', (config, initialized) => {
    if (!initialized) {
        registerListItemActions();
        registerToolbarActions();
    }
});

function registerListItemActions() {
    listItemActionRegistry.add('failed_queue.view_retry', ViewRetryMessageAction);
}

function registerToolbarActions() {
    listToolbarActionRegistry.add('failed_queue.bulk', BulkReQueueToolbarAction);
}
