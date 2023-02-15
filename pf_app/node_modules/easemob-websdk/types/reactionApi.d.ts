import { UserId } from './common';
interface ReactionMessage {
	/** The message sender. */
	from: string;
	/** The message recipient. */
	to: string;
	/** The conversation type. */
	chatType: 'singleChat' | 'groupChat';
	/** The reaction to be added to the message. The length is limited to 128 characters. */
	reactions: Reaction[];
	/** The ID of the message whose reaction updated. */
	messageId: string;
	/** The Unix timestamp for updating the reaction. */
	ts: number;
}
interface Reaction {
	/** The content of the reaction to be added to the message. The length is limited to 128 characters. */
	reaction: string;
	/** The reaction count. */
	count: number;
	/** The reaction update operation. Once the reaction is updated, the onReactionChange callback is triggered. */
	op?: {
		operator: UserId;
		reactionType: 'create' | 'delete';
	}[];
	/** The IDs of the users that added the reaction. */
	userList: UserId[];
	/** Whether the current user has added this reaction.
	 * - `true`: Yes.
	 * - `false`: No.
	 */
	isAddedBySelf?: boolean;
}
interface GetReactionDetailResult {
	/** The reaction to be added to the message. The length is limited to 128 characters. */
	reaction: string;
	/** Whether the current user added this reaction.
	 * - `true`: Yes;
	 * - `false`: No.
	 */
	isAddedBySelf: boolean;
	/** The number of users that added this reaction. */
	userCount: number;
	/** The IDs of the users who added the reaction. */
	userList: UserId[];
	/** The cursor that specifies where to start to get data. If there is data on the next page, this method will return the cursor to indicate where to start to get data for the next query. If it is `null`, the data of the first page will be returned.*/
	cursor: string;
}
interface ReactionListItem {
	/** The reaction added to the message. It cannot exceed 128 characters. */
	reaction: string;
	/** Whether the current user added this reaction.
	 * - `true`: Yes;
	 * - `false`: No.
	 */
	isAddedBySelf: boolean;
	/** The number of users that added this reaction. */
	userCount: number;
	/** The IDs of the users who added the reaction. */
	userList: UserId[];
}
interface GetReactionListResult {
	/** The ID of the message to which this reaction was added. */
	msgId: string;
	/** The reaction list of this message. */
	reactionList: ReactionListItem[];
}
export type {
	ReactionMessage,
	Reaction,
	GetReactionDetailResult,
	GetReactionListResult,
	ReactionListItem,
};
