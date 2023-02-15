/**
 * chat rooom types
 * @module TYPES
 */
import { UserId } from './common';
interface ChatRoomBaseInfo {
	/** The number of existing members. */
	affiliations_count: number;
	/** The chart room ID. */
	id: string;
	/** The chat room name. */
	name: string;
	/** The chat room owner. */
	owner: string;
}
declare type GetChatRoomsResult = ChatRoomBaseInfo[];
declare type BaseMembers =
	| {
			member: string;
	  }
	| {
			owner: string;
	  };
interface GetChatRoomDetailsResult {
	/** The list of existing members. */
	affiliations: BaseMembers[];
	/** The number of existing members. */
	affiliations_count: number;
	/** Whether to allow a group member to invite others to join the group. This method is used only for groups. - `true`: Allow; - `false`: Do not allow.*/
	allowinvites: boolean;
	/** The chat room creation time, Unix Timestamp. */
	created: number;
	/** The custom information. */
	custom: string;
	/** The chat room description. */
	description: string;
	/** The chat room ID. */
	id: string;
	/** The maximum number of members allowed in the chat room. */
	maxusers: number;
	/** Whether a user requesting to join the group requires approval from the group admin. This method is used only for groups. - `true`: Yes; - `false`: No.*/
	membersonly: boolean;
	/** Whether to mute all members. - `true`: Yes; - `false`: No.*/
	mute: boolean;
	/** The chat room name. */
	name: string;
	/** The chat room  owner. */
	owner: string;
	/** Whether it is a public group. This method is used only for groups. - `true`: Yes; - `false`: No.*/
	public: boolean;
	/** Whether the member is on the blocklist. - `true`: Yes; - `false`: No. */
	shieldgroup: boolean;
}
interface ModifyChatRoomResult {
	/** Whether the chat room description is changed successfully. - `true`: Success; - `false`: Failure.*/
	description: boolean;
	/** Whether the maximum number of members is changed successfully. - `true`: Success; - `false`: Failure. */
	maxusers: boolean;
	/** Whether the chat room name is changed successfully. - `true`: Success; - `false`: Failure. */
	groupname: boolean;
}
interface CommonRequestResult {
	/** The request result. - `true`: Success; - `false`: Failure. */
	result: boolean;
	/** The action. */
	action: string;
	/** The reason for the failure. */
	reason?: string;
	/** The user ID. */
	user: string;
	/** The chat room ID. */
	id: string;
}
interface AddUsersToChatRoomResult {
	/** The newly added members. */
	newmembers: string[];
	/** The action. */
	action: 'add_member';
	/** The chat room ID. */
	id: string;
}
declare type GetChatRoomAdminResult = UserId[];
interface SetChatRoomAdminResult {
	/** The request result. - `true`: Success; - `false`: Failure.*/
	result: boolean;
	/** The new admin. */
	newadmin: string;
}
interface RemoveChatRoomAdminResult {
	/** The request result. - `true`: Success; - `false`: Failure. */
	result: boolean;
	/** The admin to be removed. */
	oldadmin: string;
}
interface MuteChatRoomMemberResult {
	/** The request result. - `true`: Success; - `false`: Failure.*/
	result: boolean;
	/** The mute duration, in milliseconds. */
	expire: number;
	/** The member to be muted. */
	user: string;
}
interface UnmuteChatRoomMemberResult {
	/** The request result. - `true`: Success; - `false`: Failure.*/
	result: boolean;
	/** The user to be unmuted. */
	user: string;
}
interface GetChatRoomMuteListResult {
	/** The mute duration, in milliseconds. */
	expire: number;
	/** The user to be muted. */
	user: string;
}
interface WhetherAbleSendChatRoomMsgResult {
	/** The mute state. - `true`: Success; - `false`: Failure. */
	mute: boolean;
}
interface IsChatRoomWhiteUserResult {
	/** The member */
	member: string;
	/** Whether the member is on the allowlist. - `true`: Yes; - `false`: No. */
	white: boolean;
}
interface FetchChatRoomAnnouncementResult {
	/** The announcement content. */
	announcement: string;
}
interface UpdateChatRoomAnnouncementResult {
	/** The chat room ID. */
	id: string;
	/** The request result. - `true`: Success; - `false`: Failure.*/
	result: boolean;
}
interface FetchChatRoomSharedFileListResult {
	/** The file ID. */
	file_id: string;
	/** The file name. */
	file_name: string;
	/** The file owner. */
	file_owner: string;
	/** The file size. */
	file_size: number;
	/** The file upload time. */
	created: number;
}
interface DeleteChatRoomSharedFileResult {
	/** The chat room ID. */
	id: string;
	/** The file ID. */
	file_id: string;
	/** The operation result. - `true`: Success; - `false`: Failure.*/
	result: boolean;
}
interface CreateChatRoomResult {
	/** The chat room ID. */
	id: string;
}
interface GetChatroomAttributesResult {
	/** The chat room ID. */
	chatRoomId: string;
	/** The chat room attribute keys. */
	attributeKeys: Array<string>;
}
interface ChatroomAttributes {
	/** Status code. */
	status: 'success' | 'fail';
	/** The keys of failure. */
	errorKeys: {
		[key: string]: string;
	};
	/** The keys of success. */
	successKeys: Array<string>;
}
export type {
	CommonRequestResult,
	GetChatRoomsResult,
	GetChatRoomDetailsResult,
	ModifyChatRoomResult,
	GetChatRoomAdminResult,
	AddUsersToChatRoomResult,
	SetChatRoomAdminResult,
	RemoveChatRoomAdminResult,
	MuteChatRoomMemberResult,
	UnmuteChatRoomMemberResult,
	GetChatRoomMuteListResult,
	WhetherAbleSendChatRoomMsgResult,
	IsChatRoomWhiteUserResult,
	FetchChatRoomAnnouncementResult,
	UpdateChatRoomAnnouncementResult,
	FetchChatRoomSharedFileListResult,
	DeleteChatRoomSharedFileResult,
	CreateChatRoomResult,
	ChatRoomBaseInfo,
	BaseMembers,
	GetChatroomAttributesResult,
	ChatroomAttributes,
};
