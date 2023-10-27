import z from "zod";

/**
 * @typedef {typeof z.infer<typeof FailedMessageSchema>} FailedMessage
 */
export const FailedMessageSchema = z.object({
    id: z.number(),
    messageClass: z.string(),
    error: z.string(),
    errorClass: z.string(),
    errorCode: z.string(),
    failedAt: z.string(),
    messageDetails: z.string().nullish(),
    messageBusName: z.string().nullish(),
    messageOriginalTransport: z.string().nullish(),
    errorTrace: z.string().nullish(),
    failedDates: z.array(z.string()),
});

/**
 *
 * @param {unknown} data
 * @returns {FailedMessage}
 */
export const tryParseFailedMessage = (data) => FailedMessageSchema.parse(data)
