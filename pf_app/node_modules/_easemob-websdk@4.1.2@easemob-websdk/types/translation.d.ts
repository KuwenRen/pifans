interface TranslationResult {
	/** The translation results body. */
	translations: {
		/** The translation. */
		text: string;
		/** The target language of translation. */
		to: string;
	}[];
	/** The original text language detection result. */
	detectedLanguage: {
		/** The original text language. */
		language: string;
		/** The test result score (0-1). */
		score: number;
	};
}
interface SupportLanguage {
	/** The codes of the target languages. For example, the code for simplified Chinese is "zh-Hans". */
	code: string;
	/** The language name. For example, the code for simplified Chinese is "Chinese Simplified". */
	name: string;
	/** The native name of the language. For example, the native name of simplified Chinese is "Chinese (Simplified)".*/
	nativeName: string;
}
export type { TranslationResult, SupportLanguage };
