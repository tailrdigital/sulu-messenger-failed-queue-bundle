import {Requester} from "sulu-admin-bundle/services";
import {runActionOnServer} from "../utilities/run-action-on-server";
import symfonyRouting from "fos-jsrouting/router";

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
        Requester.put(symfonyRouting.generate(`tailr.messenger_failed_queue_${withRequeue ? 'requeue' : 'retry'}`), {
            identifiers: messageIdentifiers
        })
    );
}
