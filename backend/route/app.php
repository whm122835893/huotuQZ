<?php
// +----------------------------------------------------------------------
// | 路由 —— 与原站 API 路径同构（/api/... 与 /addons/...），由本项目 ThinkPHP 独立实现
// +----------------------------------------------------------------------
use think\facade\Route;

// 用户域
Route::post('api/user/login', 'user/login');
Route::post('api/user/loginBySms', 'user/loginBySms');
Route::post('api/user/register', 'user/register');
Route::post('api/user/checkSmsCode', 'user/checkSmsCode');
Route::post('api/user/resetpwd', 'user/resetpwd');
Route::post('api/user/getUserInfo', 'user/getUserInfo');

// 短信
Route::post('api/sms/send', 'sms/send');

// 首页域
Route::post('api/banner/show/getBanner', 'index/banner');
Route::post('api/index/afficheList', 'index/afficheList');
Route::post('api/index/affichetypelist', 'index/affichetypelist');
Route::post('api/index/afficheDetail/ids/:id', 'index/afficheDetail');

// 发现域（批次5：资讯/辟谣墙）
Route::post('api/discover/ant_news/list', 'discover/newsList');
Route::post('api/discover/ant_news/info', 'discover/newsInfo');
Route::post('api/discover/ant_news/category', 'discover/newsCategory');
Route::post('api/discover/rumor_wall/list', 'discover/rumorList');
Route::post('api/discover/rumor_wall/info', 'discover/rumorInfo');
Route::post('api/discover/rumor_wall/getcomment', 'discover/rumorComments');
Route::post('api/discover/rumor_wall/getReply', 'discover/rumorReplies');
Route::post('api/discover/rumor_wall/comment', 'discover/rumorComment');
Route::post('api/discover/rumor_wall/release', 'discover/rumorRelease');
Route::post('api/index/getSeries', 'index/getSeries');
Route::post('api/box/blind_box/list', 'index/blindBoxList');
Route::post('api/market/market/marketrebang', 'index/marketrebang');
Route::post('api/user/newrank1', 'index/newrank1');
Route::post('api/index/getConfig', 'index/getConfig');

// 我的域（P020）
Route::post('api/order/order/myConsignment', 'order/myConsignment');
Route::post('api/market/market/getMarketList', 'market/getMarketList');
// 市场域（P029）
Route::post('api/market/market/marketList', 'market/marketList');

// 合成域（批次6：P043-P046）
Route::post('api/synthesis/themeList', 'synthesis/themeList');
Route::post('api/synthesis/themeinfo', 'synthesis/themeinfo');
Route::post('api/synthesis/playInfo', 'synthesis/playInfo');
Route::post('api/synthesis/material', 'synthesis/material');
Route::post('api/synthesis/selectCollection', 'synthesis/selectCollection');
Route::post('api/synthesis/synthesizeNow', 'synthesis/synthesizeNow');
Route::post('api/synthesis/recordList', 'synthesis/recordList');
Route::post('api/synthesis/recordInfo', 'synthesis/recordInfo');

// 水晶/龙晶商城域（批次7：P047-P053）
Route::post('api/crystal_goods/list', 'crystal/goodsList');
Route::post('api/crystal_goods/info', 'crystal/info');
Route::post('api/crystal_goods/crystalDetail', 'crystal/crystalDetail');
Route::post('api/crystal_goods/exchangeRecord', 'crystal/exchangeRecord');
Route::post('api/crystal_goods/exchange', 'crystal/exchange');
Route::post('api/crystal_goods/getCardCode', 'crystal/getCardCode');

// 订单域（批次8：P054-P064）
Route::post('api/order/order/orderList', 'order/orderList');
Route::post('api/order/order/orderDetail/ids/<ids>', 'order/orderDetail');
Route::post('api/order/order/saleMarketOrderDetail', 'order/saleMarketOrderDetail');
Route::post('api/order/order/giveOrderDetail/ids/<ids>', 'order/giveOrderDetail');
Route::post('api/market/trade/begBuyOrderList', 'order/begBuyOrderList');
Route::post('api/market/trade/begBuyOrderInfo', 'order/begBuyOrderInfo');
Route::post('api/market/Prepayment/myOrderlist', 'order/entrustOrderList');
Route::post('api/market/Prepayment/myOrderInfo', 'order/entrustOrderInfo');

// 我的资产域（批次9：P065-P068）
Route::post('api/user_collection/user_collection/antMycollection', 'collection/antMycollection');
Route::post('api/user_collection/user_collection/getDetail', 'collection/getDetail');
Route::post('api/user_collection/user_collection/detail/ids/<ids>', 'collection/detail');
Route::post('api/user_collection/user_collection/likeCollection', 'collection/likeCollection');

// 寄售大厅域（批次10：P069-P071）
Route::post('api/user_collection/user_collection/show_resale_money', 'consignments/resaleMoney');
Route::post('api/order/order/myConsignment', 'consignments/myConsignment');

// 盲盒域（批次11：P072-P076）
Route::post('api/box/blind_box/openlog', 'blindBox/openlog');
Route::post('api/user_collection/user_collection/openbox', 'blindBox/openbox');
Route::post('api/user_collection/user_collection/openBoxs', 'blindBox/openBoxs');
Route::post('api/box/blind_box/BoxDetail/ids/<ids>', 'blindBox/boxDetail');

// 商品详情域（批次12：P077-P079）
Route::post('api/box/collection/ProductDetail/ids/<ids>', 'product/productDetail');
Route::post('api/product/relatedNotice', 'product/relatedNotice');

// 转赠域（批次13：P080-P081）
Route::post('api/user/aboutGift', 'regift/aboutGift');
Route::post('api/user/ParamsToUser', 'regift/paramsToUser');
Route::post('api/user_collection/user_collection/reGiftOrder', 'regift/reGiftOrder');

// 钱包域（批次14：P082）
Route::post('api/wallet/huifu/wallet', 'wallet/walletInfo');
Route::post('api/wallet/huifu/openAccount', 'wallet/openAccount');
Route::post('api/order/pay/CreateHuifuAccountOrder', 'wallet/createAccountOrder');
Route::post('api/user/getWalletInfo', 'wallet/getWalletInfo');

// 签到域（批次15：P083）—— 原站 needBaseUrl:false，路径不带 /api 前缀
Route::post('addons/signin/api.index/index', 'sign/indexConfig');
Route::post('addons/signin/api.index/monthSign', 'sign/monthSign');
Route::post('addons/signin/api.index/dosign', 'sign/doSign');

// 邀请域（批次16：P084-P085）
Route::post('api/user/ranking', 'invitation/ranking');
Route::post('api/user/invite', 'invitation/invite');

// 返佣域（批次17：P086-P088）—— 原站三接口均 404，如实复刻异常响应
Route::post('api/user/commison_rank', 'rebate/commisonRank');
Route::post('api/user/commision_info', 'rebate/commisionInfo');
Route::post('api/user/bei_yao_list', 'rebate/beiYaoList');

// 抽奖域（批次3 占位落地页 P092）—— 当前无活动空态
Route::post('api/lottery/info', 'lottery/info');
Route::post('api/lottery/statistics', 'lottery/statistics');
Route::post('api/lottery/record', 'lottery/record');

// 排行域（批次18：P089-P091）—— 专用端点，不改已交付首页/市场页在用的
// /api/user/newrank1 与 /api/market/market/marketrebang
Route::post('api/rank/masterList', 'rank/masterList');
Route::post('api/rank/hallOfFame', 'rank/hallOfFame');
Route::post('api/rank/hotList', 'rank/hotList');

// 抽奖活动域（批次20：P095-P099）—— P099 唯一接口，原站 404 空体如实复刻；
// P095/P097/P098 计数来自页面参数、P096 永久加载态，均零端点
Route::get('api/score/get_my_share_details', 'draw/getMyShareDetails');

// 活动域（批次21：P100 抽签 / P101 中签记录）—— 静态真实态快照
Route::get('api/draw/get', 'draw/get');
Route::post('api/draw/drawLog', 'draw/drawLog');

// 活动精选域（批次22：P102）—— 分类列表 + 活动列表，静态真实态空数组
Route::post('api/Activity/category', 'activity/category');
Route::post('api/activity/index', 'activity/index');

// 我的域二级页（批次3）：收货地址列表空态 + 流转记录参数错误，静态真实态
Route::post('api/user/address_list', 'user/addressList');
Route::post('api/user_collection/user_collection/getRecord', 'user/getRecord');

// 基础配置（后续页面逐批补充实现）
Route::miss(function () {
    return json(['code' => 0, 'msg' => '接口不存在', 'time' => (string)time(), 'data' => null]);
});
