<template>
    <view class="chart container">
        <view class="warp">
            <view>{{t('chart')}}</view>
            <view class="card">
                <view class="chart-list" ref="chartListRef">
                    <view class="chart-list__item" :class="item.type" v-for="(item, index) in messages" :key="index">
                        <template v-if="item.type == 'self'">
                            <view class="content">{{item.text}}</view><view class="label">{{item.nickname}}</view>
                        </template>
                        <template v-else>
                            <view class="label">{{item.nickname}}</view><view class="content">{{item.text}}</view>
                        </template>
                    </view>
                </view>
                <view class="chart-input">
					<u-input style="padding: 0 24rpx;margin-right: 8rpx;" v-model="content" placeholder="请输入评论内容" color="#ffffff" shape="circle" border="none"/>
					<u-button style="width: 40rpx;color: #F585FF;margin: auto;" size="mini" :text="$t('confirm')" color="rgba(245, 133, 255, 0.3)" border="none" @click="commentHandler"></u-button>
                </view>
            </view>
        </view>
    </view>
</template>

<script>
	export default {
		data() {
			return {
				
			}
		},
		methods: {
			
		}
	}
</script>

<style>

</style>
