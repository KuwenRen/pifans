<template>
	<view class="tabbar">
		<view class="tabbar-item" :class="{active: item.key == value, mid: item.key == 'home'}" v-for="(item, index) in list" @click="clickHandler(item.key)">
			<template v-if="item.key == 'home'">
				<view class="mid-warp">
					<image class="tabbar-item__icon" v-show="value !== item.key" :src="item.icon"></image>
					<image class="tabbar-item__icon" v-show="value == item.key" :src="item.activeIcon"></image>
					<view class="tabbar-item__text" :class="{active: value == item.key}">{{$t(item.text)}}</view>
				</view>
			</template>
			<template v-else>
				<image class="tabbar-item__icon" v-show="value !== item.key" :src="item.icon"></image>
				<image class="tabbar-item__icon" v-show="value == item.key" :src="item.activeIcon"></image>
				<view class="tabbar-item__text" :class="{active: value == item.key}">{{$t(item.text)}}</view>
			</template>
		</view>
	</view>
</template>

<script>
	export default {
		name:"tabbar",
		props:{
			type: {
				type: String,
				default: 'default'
			},
			value: {
				type: String,
				default: 'home'
			}
		},
		data() {
			return {
				list: [
					{
						key: 'map',
						icon: '/static/home/v2/map.png',
						activeIcon: '/static/home/v2/map-active.png',
						text: 'menu.map'
					},
					// {
					// 	key: 'ecology',
					// 	icon: '/static/home/v2/ecology.png',
					// 	activeIcon: '/static/home/v2/ecology-active.png',
					// 	text: 'menu.ecology'
					// },
					{
						key: 'shop',
						icon: '/static/home/v2/shop.png',
						activeIcon: '/static/home/v2/shop-active.png',
						text: 'menu.shop'
					},
					{
						key: 'home',
						icon: '/static/home/v2/home.png',
						activeIcon: '/static/home/v2/home-active.png',
						text: 'menu.home'
					},
					// {
					// 	key: 'knowledge',
					// 	icon: '/static/home/v2/knowledge.png',
					// 	activeIcon: '/static/home/v2/knowledge-active.png',
					// 	text: 'menu.knowledge'
					// },
					{
						key: 'web3',
						icon: '/static/home/v2/web3.png',
						activeIcon: '/static/home/v2/web3-active.png',
						text: 'menu.web3'
					},
					{
						key: 'my',
						icon: '/static/home/v2/my.png',
						activeIcon: '/static/home/v2/my-active.png',
						text: 'menu.my'
					},
				]
			};
		},
		methods: {
			clickHandler(key) {
				this.$emit('input', key);
				this.$emit('changeTab', key);
			}
		}
	}
</script>

<style lang="scss" scoped>
	.tabbar {
		position: fixed;
		bottom: 0;
		display: flex;
		justify-content: space-around;
		width: 100%;
		height: 50px;
		z-index: 1024;
		background-color: #14142B;
		padding-bottom: constant(safe-area-inset-bottom);
		padding-bottom: env(safe-area-inset-bottom);
		box-shadow: inset 0px 1px 0px 1px #232344;
		.tabbar-item {
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			text-align: center;
			.tabbar-item__icon {
				width: 24px;
				height: 24px;
			}
			.tabbar-item__text {
				margin-top: 4px;
				font-size: 10px;
				line-height: 1;
				color: rgba(255,255,255, 0.6);
			}
			&.active {
				.tabbar-item__text {
					color: #F585FF;
				}
			}
			&.mid {
				position: relative;
				width: 44px;
				.mid-warp {
					position: absolute;
					left: 0;
					right: 0;
					bottom: 0;
					height: 70px;
					text-align: center;
					display: flex;
					flex-direction: column;
					justify-content: center;
					.tabbar-item__icon {
						width: 44px;
						height: 44px;
					}
				}
			}
		}
	}
</style>
