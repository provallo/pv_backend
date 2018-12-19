<template>
    <div id="loader">
        <div id="shadow"></div>
        <div id="box"></div>
        <div id="logo">
            <img src="../assets/logo.svg" alt="Logo">
        </div>
    </div>
</template>

<style lang="less" scoped>
#loader {
    position: absolute;
    top: calc(50% - 20px);
    left: calc(50% - 20px);
}
@keyframes loader {
    0% { left: -100px }
    100% { left: 110%; }
}
#box {
    width: 50px;
    height: 50px;
    background: #fff;
    animation: animate .5s linear infinite;
    position: absolute;
    top: 0;
    left: 0;
    border-radius: 3px;
}
#logo {
    position: absolute;
    top: 8px;
    left: 5px;
    width: 40px;
    height: 40px;
    animation: logo-animate .5s linear infinite;
    img {
        max-width: 100%;
        max-height: 100%;
        animation: rotate 2s linear infinite;
    }
}
@keyframes rotate {
    0% { transform: rotate(0deg) }
    100% { transform: rotate(360deg) }
}
@keyframes logo-animate {
    17% { border-bottom-right-radius: 3px; }
    25% { transform: translateY(9px)}
    50% {
        transform: translateY(18px) scale(1,.9) ;
    }
    75% { transform: translateY(9px) }
    100% { transform: translateY(0) }
}
@keyframes animate {
    17% { border-bottom-right-radius: 3px; }
    25% { transform: translateY(9px) rotate(22.5deg); }
    50% {
        transform: translateY(18px) scale(1,.9) rotate(45deg) ;
        border-bottom-right-radius: 40px;
    }
    75% { transform: translateY(9px) rotate(67.5deg); }
    100% { transform: translateY(0) rotate(90deg); }
}
#shadow {
    width: 50px;
    height: 5px;
    background: #000;
    opacity: 0.1;
    position: absolute;
    top: 59px;
    left: 0;
    border-radius: 50%;
    animation: shadow .5s linear infinite;
}
@keyframes shadow {
    50% {
        transform: scale(1.2,1);
    }
}
</style>