/**
 *
 * @param {any} error
 * @returns {Promise<string>}
 */
export async function parseError(error)
{
    if (error instanceof Response) {
        return await error.json().then(
            (data) => {
                return data?.detail ?? 'Unable to fetch HTTP resource: ' + error.statusText;
            },
            () => 'Unable to fetch HTTP resource: ' + error.statusText,
        )
    }

    if (error instanceof Error) {
        return error.message;
    }

    return `${error}`
}
