import React, {useState, ReactNode} from "react";
import {Checkbox, Dialog} from "sulu-admin-bundle/components";
import {translate} from "sulu-admin-bundle/utils";
import MessagesDetails from "./MessageDetails";
import {retryMessage} from "../api/retryMessage";

/**
 * @typedef {import('../types/failed-message').FailedMessage} FailedMessage
 * @typedef {{
 *   failedMessage: FailedMessage,
 *   onCancel: () => void,
 *   afterRetry: () => void,
 * }} Props
 */

/**
 * @param {FailedMessage} failedMessage
 * @returns {{
 *     retryAction: (boolean) => Promise<void>,
 *     executed: boolean,
 *     loading: boolean,
 *     error: Error|undefined
 * }}
 */
const useRetryMessageLogic = ({failedMessage}) => {
    const [executed, setExecuted] = useState(false);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(undefined);

    const retryAction = async (withRequeue) => {
        setExecuted(false);
        setLoading(true);
        setError(undefined);

        try {
            await retryMessage(failedMessage.id, withRequeue);
        } catch (error) {
            setError(error);
        }

        setLoading(false);
        setExecuted(true);
    }

    return {loading, executed, error, retryAction};
}


const errorStyle = {color: "red"};
const successStyle = {color: "green"};

/**
 *
 * @param {Props} props
 * @returns {ReactNode}
 */
function ViewRetryMessageDialog(props) {
    const [withRequeue, setWithRequeue] = useState(true);
    const {failedMessage, onCancel, afterRetry} = props;
    const {loading, executed, error, retryAction} = useRetryMessageLogic(props);

    return (
        <>
            <Dialog
                title={(error) ? translate('Error details') : translate('Success details')}
                open={executed}
                snackbarType={(error) ? 'error' : 'success'}
                confirmText={translate('Ok')}
                onConfirm={afterRetry}
            >
                {(error)
                    ? <div style={errorStyle}><strong>{translate('An error occurred during the retry action:')}</strong> {error?.message}</div>
                    : <div style={successStyle}>{(withRequeue)
                        ? translate('The message was successfully pushed to the original transport/queue.')
                        : translate('The message was successfully processed synchronously.')  }</div>
                }
            </Dialog>
            <Dialog
                size={"large"}
                title={translate('Message #') + failedMessage.id}
                open={true}
                align={'left'}
                snackbarType="info"
                cancelText={translate('Close')}
                confirmText={(withRequeue) ? translate('Retry in requeue mode') : translate('Retry in sync mode') }
                onCancel={onCancel}
                onConfirm={() => retryAction(withRequeue)}
                confirmLoading={loading}
            >
                <MessagesDetails failedMessage={failedMessage} colSpanLabel={3} colSpanValue={9}/>
                <div style={{margin: "1rem 0"}}>
                    <h2>{translate('Retry with requeue')}</h2>
                    <Checkbox checked={withRequeue} onChange={ (checked) => setWithRequeue(checked)}>
                        {translate('Execute the retry by pushing to the original transport/queue.')}
                    </Checkbox>
                    {(!withRequeue) &&
                        <div style={errorStyle}>
                            {translate('Note: The message will be executed synchronously. Depending on the type of task, this may take a while and possibly fail due to a server-timeout.')}
                        </div>
                    }
                </div>
            </Dialog>
        </>
    );
}

export default ViewRetryMessageDialog;
