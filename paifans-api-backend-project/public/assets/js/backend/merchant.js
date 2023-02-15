define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'merchant/index' + location.search,
                    add_url: 'merchant/add',
                    edit_url: 'merchant/edit',
                    del_url: 'merchant/del',
                    multi_url: 'merchant/multi',
                    import_url: 'merchant/import',
                    table: 'merchant',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {
                            field: 'business', title: __('商家类别'), operate: false
                        },
                        {
                            field: 'images',
                            title: __('Image'),
                            operate: false,
                            events: Table.api.events.image,
                            formatter: Table.api.formatter.images
                        },
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'user.username', title: __('用户名称'), operate: false},
                        {field: 'phone', title: __('Phone'), operate: 'LIKE'},
                        {field: 'address', title: __('Address'), operate: 'LIKE'},
                        {field: 'lat', title: __('纬度'), operate: 'LIKE'},
                        {field: 'lng', title: __('经度'), operate: 'LIKE'},
                        {
                            field: 'status',
                            title: __('Status'),
                            searchList: {
                                "0": __('Status 0'),
                                "1": __('Status 1'),
                                "2": __('Status 2'),
                                "3": __('Status 3')
                            },
                            formatter: Table.api.formatter.status
                        },
                        {
                            field: 'createtime',
                            title: __('Createtime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            autocomplete: false,
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'updatetime',
                            title: __('Updatetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            autocomplete: false,
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'audit',
                                    text: __('审核'),
                                    title: __('审核'),
                                    classname: 'btn btn-xs btn-info btn-dialog',
                                    // icon: 'fa fa-arrow-up',
                                    visible: function (row) {
                                        if (row.status === "1" && row.group_id !== 3) {
                                            return true;
                                        }
                                    },
                                    url: 'merchant/audit'
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            table.on('post-body.bs.table', function () {
                $(".btn-dialog").data("area", ['60%', '60%']);
            });
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        audit: function () {
            $(document).on("click", "input[type=radio][name='row[status]']",
                function () {
                    console.log($(this).val())
                    if ($(this).val() === "2") {
                        $('form').validator("setField", "row[reason]", "required;");
                        $("#audit").css("display", "block");
                    } else {
                        $('form').validator("setField", "row[reason]", null);
                        $("#audit").css("display", "none");
                    }
                });
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