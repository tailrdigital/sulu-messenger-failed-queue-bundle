import {Requester} from "sulu-admin-bundle/services";
import {runActionOnServer} from "../utilities/run-action-on-server";

/**
 * @param {number} messageId
 * @param {boolean} withRequeue
 * @returns {Promise<void>}
 */
export async function retryMessage(
    messageId,
    withRequeue,
) {
    await runActionOnServer(
        Requester.put(`/admin/api/messenger-failed-queue/${messageId}/${withRequeue ? 'requeue' : 'retry'}`, {})
    );
}
