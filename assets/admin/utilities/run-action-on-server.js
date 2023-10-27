import {parseError} from "./parse-error";

/**
 * @template T
 * @param {Promise<T>} action
 * @returns {Promise<T>}
 */
export function runActionOnServer(action) {
    return action.catch(async (error) => {
        throw new Error(await parseError(error));
    });
}
