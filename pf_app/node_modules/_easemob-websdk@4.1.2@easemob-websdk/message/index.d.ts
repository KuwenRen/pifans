import {
	ReadMsgBody,
	CreateReadMsgParameters,
	ReadMsgSetParameters,
	ReadParameters,
} from './read';
import {
	DeliveryMsgBody,
	CreateDeliveryMsgParameters,
	DeliveryMsgSetParameters,
	DeliveryParameters,
} from './delivery';
import {
	ChannelMsgBody,
	CreateChannelMsgParameters,
	ChannelMsgSetParameters,
	ChannelParameters,
} from './channel';
import {
	TextMsgBody,
	CreateTextMsgParameters,
	TextMsgSetParameters,
	TextParameters,
} from './text';
import {
	CmdMsgBody,
	CreateCmdMsgParameters,
	CmdMsgSetParameters,
	CmdParameters,
} from './cmd';
import {
	CustomMsgBody,
	CreateCustomMsgParameters,
	CustomMsgSetParameters,
	CustomParameters,
} from './custom';
import {
	LocationMsgBody,
	CreateLocationMsgParameters,
	LocationMsgSetParameters,
	LocationParameters,
} from './location';
import {
	ImgMsgBody,
	CreateImgMsgParameters,
	ImgMsgSetParameters,
	ImgParameters,
} from './img';
import {
	AudioMsgBody,
	CreateAudioMsgParameters,
	AudioMsgSetParameters,
	AudioParameters,
} from './audio';
import {
	VideoMsgBody,
	CreateVideoMsgParameters,
	VideoMsgSetParameters,
	VideoParameters,
} from './video';
import {
	FileMsgBody,
	CreateFileMsgParameters,
	FileMsgSetParameters,
	FileParameters,
} from './file';
export type MessageType =
	| 'read'
	| 'delivery'
	| 'channel'
	| 'txt'
	| 'cmd'
	| 'custom'
	| 'loc'
	| 'img'
	| 'audio'
	| 'file'
	| 'video';
export declare type NewMessageParamters =
	| ReadParameters
	| DeliveryParameters
	| ChannelParameters
	| TextParameters
	| CmdParameters
	| CustomParameters
	| LocationParameters
	| ImgParameters
	| AudioParameters
	| VideoParameters
	| FileParameters;
export declare type MessageSetParameters =
	| ReadMsgSetParameters
	| DeliveryMsgSetParameters
	| ChannelMsgSetParameters
	| TextMsgSetParameters
	| CmdMsgSetParameters
	| CustomMsgSetParameters
	| LocationMsgSetParameters
	| ImgMsgSetParameters
	| AudioMsgSetParameters
	| VideoMsgSetParameters
	| FileMsgSetParameters;
export declare type CreateMsgType =
	| CreateTextMsgParameters
	| CreateImgMsgParameters
	| CreateCmdMsgParameters
	| CreateFileMsgParameters
	| CreateVideoMsgParameters
	| CreateCustomMsgParameters
	| CreateLocationMsgParameters
	| CreateChannelMsgParameters
	| CreateDeliveryMsgParameters
	| CreateReadMsgParameters
	| CreateAudioMsgParameters;
export declare type MessageBody =
	| ReadMsgBody
	| DeliveryMsgBody
	| ChannelMsgBody
	| TextMsgBody
	| CmdMsgBody
	| CustomMsgBody
	| LocationMsgBody
	| ImgMsgBody
	| AudioMsgBody
	| VideoMsgBody
	| FileMsgBody;
declare class Message {
	id: string;
	body: any;
	type: MessageType;
	constructor(type: MessageType, id?: string);
	/** @hidden */
	static createOldMsg(options: NewMessageParamters): any;
	/** Creates a message. */
	/** 创建消息。*/
	static create(options: CreateMsgType): MessageBody;
	/**
	 * @hidden
	 * Set the message body.
	 */
	/**
	 * @hidden
	 * 设置消息体。
	 */
	set(options: MessageSetParameters): void;
}
export { Message };
