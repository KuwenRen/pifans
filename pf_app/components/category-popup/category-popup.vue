<!-- 分类弹窗列表 -->
<template>
	<view class="category-popup">
		<view v-for="(item,key) in datalist" :key="key" class="item" :class="item.id==id?'active':''"
			@click="categoryClick(item)">
			<template v-if="checked">
				<text>{{item.typename||item.name}}</text>
				<image v-if="item.checked" class="icon" src="../../static/img/ok.png" mode="heightFix"></image>
			</template>
			<template v-else>
				<text v-if="localeIndex==1">{{item.typename||item.name}}</text>
				<text v-if="localeIndex==2">{{item.typename||item.name1}}</text>
				<image v-if="item.id==id" class="icon" src="../../static/img/ok.png" mode="heightFix"></image>
			</template>
		</view>
	</view>
</template>

<script>
	import {
		mapState
	} from 'vuex';
	export default {
		props: {
			categoryId: [String, Number],
			datalist: Array,
			checked: { //默认单选
				type: Boolean,
				default: false
			}
		},
		model: {
			prop: 'categoryId',
			event: 'handleValueChange'
		},
		computed: {
			...mapState({
				locale: state => state.locale
			}),
			localeIndex() {
				return this.locale == 'zh-cn' ? 1 : 2;
			},
			id: {
				get() {
					return this.categoryId
				},
				set(id) {
					this.$emit('handleValueChange', id);
				}
			},
		},
		data() {
			return {

			}
		},
		methods: {
			categoryClick(item) {
				if (this.checked) {
					this.$emit('categoryClick', item);
				} else {
					this.id = item.id;
				}
			}
		}
	}
</script>

<style lang="scss" scoped>
	.category-popup {
		width: 100%;
		position: absolute;
		left: 0;
		background-color: #14142B;
		border-radius: 0 0 4px 4px;
		z-index: 99;
		overflow: hidden;

		.item {
			margin-bottom: 16rpx;
			padding: 0 32rpx;
			display: flex;
			align-items: center;
			justify-content: space-between;
			font-size: 30rpx;

			.icon {
				height: 26rpx;
			}

			&.active {
				color: #8D3FFA;
			}
		}
	}
</style>
