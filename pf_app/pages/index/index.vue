<template>
	<view class="index-layout">
		<myTab v-if="index == 'my'" @changeTab="changeTab" />
		<mapTab v-if="index == 'map'" @changeTab="changeTab" @changeIndex="changeIndex" />
		<homeTab v-if="index == 'home'" @changeTab="changeTab" @changeIndex="changeIndex" />
		<!-- <ecologyTab v-if="index == 'ecology'" @changeTab="changeTab"/> -->
		<!-- <knowledgeTab v-if="index == 'knowledge'" @changeTab="changeTab"/> -->
		<shopTab v-if="index == 'shop'" @changeTab="changeTab" />
		<web3Tab v-if="index == 'web3'" @changeTab="changeTab" />
		<labelComp v-if="index == 'label'" @changeIndex="changeIndex" />
		<telegramComp v-if="index == 'telegram'" @changeTab="changeTab" />
		<informationComp v-if="index == 'information'" @changeTab="changeTab" />
		<tabbar v-model="active" @changeTab="changeTab" />
	</view>
</template>

<script>
	import myTab from '../my/index.vue';
	import mapTab from '../map/index.vue';
	import web3Tab from '../web3/web3.vue';
	import shopTab from '../shoplist/shoplist.vue';
	import homeTab from '../home/index.vue';
	import ecologyTab from '../ecology/index.vue';
	import knowledgeTab from '../article/knowledge.vue';

	import labelComp from '../map/label.vue';
	import telegramComp from '../telegram/telegram.vue';
	import informationComp from '../article/information.vue';

	// import tabbar from '@/components/tabbar/tabbar.nvue'
	export default {
		components: {
			myTab,
			mapTab,
			web3Tab,
			shopTab,
			homeTab,
			ecologyTab,
			labelComp,
			knowledgeTab,
			telegramComp,
			informationComp
		},
		data() {
			return {
				active: 'home',
				index: 'home',
			}
		},
		methods: {
			changeTab(tab, index) {
				this.active = tab;
				this.index = index ? index : tab;
			},
			changeIndex(index) {
				this.index = index;
			}
		},
		onLoad({
			tab,
			index
		}) {
			tab && this.changeTab(tab, index);
		}
	}
</script>

<style lang="scss" scoped>
	.index-layout {
		background-color: #050523;
	}
</style>
