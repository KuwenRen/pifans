<!-- 
@author x 
@title 国家选择
 -->
<template>
	<view class="country-choose">
		<u-header title="国家选择"></u-header>
		<view style="padding: 32rpx;">
			<u-search color="#ffffff" bgColor="#323256" searchIconColor="#C4C4C4" borderColor="#404065"
				placeholderColor="rgba(255,255,255,0.8)" :placeholder="$t('searchPlace')" height="80" shape="square"
				:showAction="searchWords!=''" v-model="searchWords" border-color @custom="searchHnadler"></u-search>
		</view>
		<view class="list">
			<view v-for="(item,key) in datalist" :key="key" class="item" @click="countryChoose(item)">
				<text>{{item.name}}</text>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		getCountry
	} from '@/api/user'
	export default {
		data() {
			return {
				searchWords: '', //搜索关键词
				datalist: [] //数据列表
			}
		},
		onLoad() {
			this.getCountry();
		},
		methods: {
			/* 搜索 */
			searchHnadler() {
				this.datalist = this.datalist.filter(item => {
					return item.name.indexOf(this.searchWords) >= 0
				})
			},
			/* 获取国家列表 */
			getCountry() {
				getCountry().then(res => {
					this.datalist = res.data;
				})
			},
			/* 国家选取 */
			countryChoose(item) {
				this.$store.state.country = item;
				uni.navigateBack();
			}
		}
	}
</script>

<style lang="scss" scoped>
	.item {
		margin-top: 16rpx;
		padding: 16rpx 32rpx;
		color: #fff;
		font-weight: bold;
		background-color: #14142B;
	}
</style>
