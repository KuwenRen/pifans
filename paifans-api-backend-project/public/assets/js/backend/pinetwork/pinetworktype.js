define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'pinetwork/pinetworktype/index' + location.search,
                    add_url: 'pinetwork/pinetworktype/add',
                    edit_url: 'pinetwork/pinetworktype/edit',
                    del_url: 'pinetwork/pinetworktype/del',
                    multi_url: 'pinetwork/pinetworktype/multi',
                    import_url: 'pinetwork/pinetworktype/import',
                    table: 'pinetwork_type',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'typename', title: __('Typename'), operate: 'LIKE'},
                        {field: 'typename1', title: __('英文版类型'), operate: 'LIKE'},
                        {
                            field: 'bg_image',
                            title: "背景图",
                            operate: false,
                            events: Table.api.events.image,
                            formatter: Table.api.formatter.image
                        },
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'update_time', title: __('Update_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {
                            field: 'status',
                            title: __('状态(打开/关闭)'),
                            table: table,
                            searchList:{0:"关闭",1:"打开"},
                            formatter: Table.api.formatter.toggle
                        },
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
