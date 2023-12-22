import {Requester} from "sulu-admin-bundle/services";
import {runActionOnServer} from "../utilities/run-action-on-server";

/**
 * @param {number[]} messageIdentifiers
 * @param {boolean} withRequeue
 * @returns {Promise<void>}
 */
export async function retryMessage(
    messageIdentifiers,
    withRequeue,
) {
    await runActionOnServer(
        Requester.put(`/admin/api/messenger-failed-queue/${withRequeue ? 'requeue' : 'retry'}`, {
            identifiers: messageIdentifiers
        })
    );
}
