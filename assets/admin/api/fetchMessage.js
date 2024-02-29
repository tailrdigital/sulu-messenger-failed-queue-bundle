import {Requester} from "sulu-admin-bundle/services";
import {runActionOnServer} from "../utilities/run-action-on-server";
import {tryParseFailedMessage} from "../types/failed-message";
import symfonyRouting from "fos-jsrouting/router";

/**
 * @param {number} messageId
 * @returns {Promise<import('../types/failed-message').FailedMessage>}
 */
export async function fetchMessage(
    messageId,
) {
    const response = await runActionOnServer(
        Requester.get(symfonyRouting.generate('tailr.messenger_failed_queue_fetch', {id: messageId}))
    );

    return tryParseFailedMessage(response);
}
