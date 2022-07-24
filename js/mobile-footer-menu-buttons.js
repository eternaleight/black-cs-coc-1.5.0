//フッターモバイルメニュー
// var footerMenu = document.getElementById(".mobile-footer-menu-buttons");
// var footerHeight = footerMenu.offsetHeight;
// var footerStartPos = 0;
// var scrollTops = document.getElement.scrollTop || doucument.body.scrollTop;
// var footerCurrentPos = scrollTops;

// if (footerCurrentPos - footerStartPos > 20) {
//   if(footerCurrentPos >= 100) {
//     footerMenu.css("bottom","calc( -1 * (env(safe-area-inset-bottom) + " + footerHeight + "px) )");
//   }
// } else if (footerCurrentPos - footerStartPos < -8) {
//   footerMenu.css("bottom", 0);
// }

// if (footerCurrentPos > footerStartPos) {
//   if(footerCurrentPos >= 100) {
//     footerMenu.style.cssText(`bottom: calc( -1 * (env(safe-area-inset-bottom) + ${footerHeight}px) )`);
//   }
// } else if (footerCurrentPos - footerStartPos < -8) {
//   footerMenu.style.cssText("bottom: 0");
// }

// footerStartPos = footerCurrentPos;

//フッターモバイルメニュー
// var footerMenu = $(".mobile-footer-menu-buttons");
// var footerHeight = footerMenu.outerHeight();
// var footerStartPos = 0;
// $(window).scroll(function(){
//   var footerCurrentPos = $(this).scrollTop();
//
//   // if (footerCurrentPos - footerStartPos > 20) {
//   //   if(footerCurrentPos >= 100) {
//   //     footerMenu.css("bottom","calc( -1 * (env(safe-area-inset-bottom) + " + footerHeight + "px) )");
//   //   }
//   // } else if (footerCurrentPos - footerStartPos < -8) {
//   //   footerMenu.css("bottom", 0);
//   // }
//
//   if (footerCurrentPos > footerStartPos) {
//     if(footerCurrentPos >= 100) {
//       footerMenu.css("bottom","calc( -1 * (env(safe-area-inset-bottom) + " + footerHeight + "px) )");
//     }
//   } else if (footerCurrentPos - footerStartPos < -8) {
//     footerMenu.css("bottom", 0);
//   }
//
//   footerStartPos = footerCurrentPos;
// });
