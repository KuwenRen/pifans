/**
 * improve push types
 * @module TYPES
 */
interface SilentModeConversationType {
	/** Silent mode type. */
	type: string;
	/** Silent mode duration, duration of Unix timestamp. */
	ignoreDuration: number;
	/** Silent mode interval. */
	ignoreInterval: string;
}
interface ConversationSilentModeType {
	/** User push map. */
	user: {
		[propname: string]: SilentModeConversationType;
	};
	/** Group push map. */
	group: {
		[propname: string]: SilentModeConversationType;
	};
}
interface TranslationLanguageType {
	/** Translation language. */
	language: string;
}
declare enum SILENTMODETYPE {
	/** All message. */
	ALL = 'ALL',
	/** @ message of myself. */
	AT = 'AT',
	/** None. */
	NONE = 'NONE',
}
declare enum CONVERSATIONTYPE {
	/** Single chat. */
	SINGLECHAT = 'singleChat',
	/** Group chat. */
	GROUPCHAT = 'groupChat',
	/** Chat room. */
	CHATROOM = 'chatRoom',
}
declare type ConversationListType = {
	/** Conversation id. */
	id: string;
	/** Conversation type. */
	type: string;
};
interface SilentModeRemindType {
	/** Silent mode type. */
	paramType: 0;
	/** Silent mode type. */
	remindType: SILENTMODETYPE;
}
interface SilentModeDuration {
	/** Silent mode type. */
	paramType: 1;
	/** Silent mode duration, duration of Unix timestamp. */
	duration: number;
}
interface SilentModeInterval {
	/** Silent mode type. */
	paramType: 2;
	/** Start time interval. */
	startTime: Interval;
	/** End time interval. */
	endTime: Interval;
}
export declare type Interval = {
	/** Hours. */
	hours: number;
	/** Minutes. */
	minutes: number;
};
declare type SilentModeParamType =
	| SilentModeRemindType
	| SilentModeDuration
	| SilentModeInterval;
export type {
	SilentModeConversationType,
	ConversationSilentModeType,
	TranslationLanguageType,
	SILENTMODETYPE,
	CONVERSATIONTYPE,
	ConversationListType,
	SilentModeParamType,
	SilentModeRemindType,
	SilentModeDuration,
	SilentModeInterval,
};
