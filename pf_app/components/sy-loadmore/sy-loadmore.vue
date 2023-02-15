<template>
	<view class="sy-loadmore">
		<u-loadmore :status="status" :loadmoreText="$t('loadmore')" :loadingText="$t('loading')" :nomoreText="$t('nomore')" line/>
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	export default {
		name:"sy-loadmore",
		props: {
			status: String
		},
		data() {
			return {
				
			};
		},
		computed: {
			
		}
	}
</script>

<style lang="scss" scoped>
	.sy-loadmore {
		padding: 30rpx 20rpx;
		display: flex;
		justify-content: center;
	}
</style>
