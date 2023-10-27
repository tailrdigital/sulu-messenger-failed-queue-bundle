import {listItemActionRegistry} from 'sulu-admin-bundle/views';
import {initializer} from 'sulu-admin-bundle/services';
import ViewRetryMessageAction from "./components/ViewRetryMessageAction";

initializer.addUpdateConfigHook('sulu_admin', (config, initialized) => {
    if (!initialized) {
        registerListItemActions();
    }
});

function registerListItemActions() {
    listItemActionRegistry.add('failed_queue.view_retry', ViewRetryMessageAction);
}
