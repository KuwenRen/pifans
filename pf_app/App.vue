<script>
	import store from '@/store/index.js'
	export default {

	};
</script>

<style lang="scss">
	@import url("https://at.alicdn.com/t/font_3400691_8oviahxfy1c.css");
	/*每个页面公共css */
	@import "@/uni_modules/uview-ui/index.scss";
	@import url("/static/iconfont.css");

	page {
		background-color: #050523;
	}

	/*隐藏head标签*/
	uni-page-head {
		display: none;
	}

	/*body高度100%*/
	uni-page-wrapper {
		height: 100% !important;
	}


	.btn-all {
		background-image: linear-gradient(to right, #E323FF, #9041FF);
		padding: 20rpx;
		border-radius: 10rpx;
		font-size: 30rpx;
		color: #FFF;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.u-input,
	.u-textarea {
		border: none !important;
	}

	.u-form-item__body__left__content__label,
	textarea {
		color: #FFF !important;
	}

	.container {

		&.my,
		&.map &.home,
		&.ecology {
			padding-top: var(--status-bar-height);
		}

		box-sizing: border-box;
		min-height: 100vh;
		background-color: #050523;

		.warp {
			padding-left: 40rpx;
			padding-right: 40rpx;
		}
	}

	.sy-form {
		.u-border {
			border-color: transparent !important;
		}

		.code-input {
			border-top-right-radius: 0 !important;
			border-bottom-right-radius: 0 !important;
		}

		.code-btn {
			height: 44px !important;
			color: #BEBEDC !important;
			border-top-left-radius: 0 !important;
			border-bottom-left-radius: 0 !important;
		}
	}

	.sy-header {
		display: flex;
		margin-bottom: 48rpx;
		padding-bottom: 30rpx;
		border-bottom: 1rpx solid #3A3A52;
		font-size: 32rpx;
		line-height: 50rpx;

		.sy-header__inner {
			position: relative;
			font-size: 40rpx;
			line-height: 50rpx;
			color: #F585FF;

			&::after {
				content: "";
				position: absolute;
				left: 0;
				bottom: -31rpx;
				width: 100%;
				height: 8rpx;
				border-radius: 2rpx;
				background: linear-gradient(265deg, #E323FF 0%, #9041FF 100%);
			}
		}
	}
</style>
