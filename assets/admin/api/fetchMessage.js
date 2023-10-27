import {Requester} from "sulu-admin-bundle/services";
import {runActionOnServer} from "../utilities/run-action-on-server";
import {tryParseFailedMessage} from "../types/failed-message";

/**
 * @param {number} messageId
 * @returns {Promise<import('../types/failed-message').FailedMessage>}
 */
export async function fetchMessage(
    messageId,
) {
    const response = await runActionOnServer(
        Requester.get(`/admin/api/messenger-failed-queue/${messageId}`)
    );

    return tryParseFailedMessage(response);
}
