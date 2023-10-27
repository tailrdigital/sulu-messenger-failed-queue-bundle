import React, {ReactNode} from "react";
import {observable, action, runInAction} from "mobx";
import {AbstractListItemAction} from "sulu-admin-bundle/views";
import ViewRetryMessageDialog from "./ViewRetryMessageDialog";
import {fetchMessage} from "../api/fetchMessage";

/**
 * @typedef {import('../types/failed-message.js').FailedMessage} FailedMessage
 */
export default class ViewRetryMessageAction extends AbstractListItemAction {
    /**
     * @type {FailedMessage|null|undefined}
     */
    @observable selectedMessage = null;

    /**
     *
     * @param {FailedMessage|undefined} failedMessage
     * @returns {Object}
     */
    getItemActionConfig(failedMessage) {
        return {
            icon: 'su-eye',
            disabled: false,
            onClick: failedMessage ? () => this.handleClick(failedMessage) : undefined,
        };
    }

    /**
     * @returns {ReactNode}
     */
    getNode(){
        if (!this.selectedMessage) {
            return null;
        }

        return (
            <ViewRetryMessageDialog
                key={'retry-message-dialog'}
                failedMessage={this.selectedMessage}
                onCancel={() => { this.resetSelectedMessage() }}
                afterRetry={() => { this.afterRetry() }}/>
        );
    }

    @action afterRetry() {
        this.resetSelectedMessage();
        this.listStore.reload();
    }

    /**
     *
     * @param {FailedMessage|undefined} item
     * @return {Promise<void>}
     */
    @action async handleClick(item) {
        if (!item) {
           return;
        }

        runInAction(async () => {
            this.setSelectedMessage(await fetchMessage(item.id));
        })
    }

    /**
     * @param {FailedMessage} item
     */
    @action setSelectedMessage(item) {
        this.selectedMessage = item;
    }

    @action resetSelectedMessage() {
        this.selectedMessage = null;
    }
}
