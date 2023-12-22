import React, {ReactNode} from "react";
import {action, observable, runInAction} from "mobx";
import {errorStyle, successStyle} from "../types/style";
import {retryMessage} from "../api/retryMessage";

import {AbstractListToolbarAction} from "sulu-admin-bundle/views";
import {Dialog} from "sulu-admin-bundle/components";
import {translate} from "sulu-admin-bundle/utils";

export default class BulkReQueueToolbarAction extends AbstractListToolbarAction {

    /**
     * @type {boolean}
     */
    @observable executed = false;

    /**
     * @type {Error|null}
     */
    @observable error = null;

    getToolbarItemConfig() {
        return {
            type: 'button',
            label: translate('Bulk requeue'),
            icon: 'su-clean',
            disabled: this.listStore.selections.length === 0,
            onClick: action(async () => await this.handleClick())
        };
    }

    @action cleanup = () => {
        runInAction(() => {
            this.executed = false;
            this.error = null;
        })
        this.listStore.reload();
    }

    /**
     * @returns {ReactNode}
     */
    getNode(){
        return (
            <Dialog
                title={(this.error) ? translate('Error details') : translate('Success details')}
                open={this.executed?? false}
                snackbarType={(this.error) ? 'error' : 'success'}
                confirmText={translate('Ok')}
                onConfirm={this.cleanup}
            >
                {(this.error)
                    ? (
                        <div style={errorStyle}>
                            <strong>{translate('An error occurred during the retry action:')}</strong> {this.error?.message}
                        </div>
                    ) : (
                        <div style={successStyle}>
                            {translate('The message was successfully pushed to the original transport/queue.')}
                        </div>
                    )
                }
            </Dialog>
        )
    }

    @action handleClick = async () => {
        if (this.listStore.selections.length === 0) {
            return;
        }

        /**
         * @type {number[]}
         */
        const selectedCommandIds = this.listStore.selections.map((item) => item.id);

        try {
            await retryMessage(selectedCommandIds, true);
        } catch (error) {
            runInAction(() => {
                this.error = error;
            })
        } finally {
            runInAction(() => {
                this.executed = true
            })
        }
    };
}