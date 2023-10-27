import React, {ReactNode} from "react";
import {translate} from "sulu-admin-bundle/utils";
import {Grid} from "sulu-admin-bundle/components";
import Item from "sulu-admin-bundle/components/Grid/Item";
import TextArea from "sulu-admin-bundle/components/TextArea";
import moment from "moment";

/**
 * @typedef {import('../types/failed-message.js').FailedMessage} FailedMessage
 * @typedef {0 | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | 11 | 12} Colspan
 * @typedef {{
 *   failedMessage: FailedMessage,
 *   colSpanLabel: Colspan,
 *   colSpanValue: Colspan,
 * }} Props
 *
 * @param {Props} props
 *
 * @returns {ReactNode}
 */
export default function MessagesDetails(props) {
    const {failedMessage, colSpanLabel, colSpanValue} = props;

    return (
        <>
            <div style={{margin: "1rem 0"}}>
                <h2>{translate('Message information')}</h2>
                <Grid>
                    <Item colSpan={colSpanLabel}>{translate('Message ID')}</Item>
                    <Item colSpan={colSpanValue}>{failedMessage.id}</Item>
                </Grid>
                <Grid>
                    <Item colSpan={colSpanLabel}>{translate('Message class')}</Item>
                    <Item colSpan={colSpanValue}>{failedMessage.messageClass}</Item>
                </Grid>
                <Grid>
                    <Item colSpan={colSpanLabel}>{translate('Message details')}</Item>
                    <Item colSpan={colSpanValue}>
                        <TextArea onChange={() => {}} disabled={true} value={failedMessage.messageDetails}/>
                    </Item>
                </Grid>
                <Grid>
                    <Item colSpan={colSpanLabel}>{translate('Message bus')}</Item>
                    <Item colSpan={colSpanValue}>{failedMessage.messageBusName}</Item>
                </Grid>
                <Grid>
                    <Item colSpan={colSpanLabel}>{translate('Message original transport')}</Item>
                    <Item colSpan={colSpanValue}>{failedMessage.messageOriginalTransport}</Item>
                </Grid>
            </div>
            <div style={{margin: "1rem 0"}}>
                <h2>{translate('Error details')}</h2>
                <Grid>
                    <Item colSpan={colSpanLabel}>{translate('Error message')}</Item>
                    <Item colSpan={colSpanValue}>{failedMessage.error}</Item>
                </Grid>
                <Grid>
                    <Item colSpan={colSpanLabel}>{translate('Error class')}</Item>
                    <Item colSpan={colSpanValue}>{failedMessage.errorClass}</Item>
                </Grid>
                <Grid>
                    <Item colSpan={colSpanLabel}>{translate('Error code')}</Item>
                    <Item colSpan={colSpanValue}>{failedMessage.errorCode}</Item>
                </Grid>
                <Grid>
                    <Item colSpan={colSpanLabel}>{translate('Failed at')}</Item>
                    <Item colSpan={colSpanValue}>{moment(failedMessage.failedAt, moment.ISO_8601).format('LLL')}</Item>
                </Grid>
                <Grid>
                    <Item colSpan={colSpanLabel}>{translate('Exception details')}</Item>
                    <Item colSpan={colSpanValue}>
                        <TextArea onChange={() => {}} disabled={true} value={failedMessage.errorTrace}/>
                    </Item>
                </Grid>
                <Grid>
                    <Item colSpan={colSpanLabel}>{translate('Fail/retry history')}</Item>
                    <Item colSpan={colSpanValue}>
                        {failedMessage.failedDates.map(
                            (failedAt, key) =>  <div key={'failed-at-' + key}>{moment(failedAt, moment.ISO_8601).format('LLL')}</div> )
                        }
                    </Item>
                </Grid>
            </div>
        </>
    );
}
