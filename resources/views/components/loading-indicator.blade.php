<div style="display: none;" wire:loading id="loading_indicator">
    <div
        style="display: flex; justify-content: center; align-items: center; background-color: black; position: fixed; top: 0px; left: 0px; z-index: 9999; width: 100%; height: 100%; opacity: .55;">
        <div style="color: #0cd468" class="la-ball-fall">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
</div>

<style>
    .la-ball-fall,
    .la-ball-fall > div {
        position: relative;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }

    .la-ball-fall {
        display: block;
        font-size: 0;
        color: #fff;
    }

    .la-ball-fall.la-dark {
        color: #333;
    }

    .la-ball-fall > div {
        display: inline-block;
        float: none;
        background-color: currentColor;
        border: 0 solid currentColor;
    }

    .la-ball-fall {
        width: 54px;
        height: 18px;
    }

    .la-ball-fall > div {
        width: 10px;
        height: 10px;
        margin: 4px;
        border-radius: 100%;
        opacity: 0;
        -webkit-animation: ball-fall 1s ease-in-out infinite;
        -moz-animation: ball-fall 1s ease-in-out infinite;
        -o-animation: ball-fall 1s ease-in-out infinite;
        animation: ball-fall 1s ease-in-out infinite;
    }

    .la-ball-fall > div:nth-child(1) {
        -webkit-animation-delay: -200ms;
        -moz-animation-delay: -200ms;
        -o-animation-delay: -200ms;
        animation-delay: -200ms;
    }

    .la-ball-fall > div:nth-child(2) {
        -webkit-animation-delay: -100ms;
        -moz-animation-delay: -100ms;
        -o-animation-delay: -100ms;
        animation-delay: -100ms;
    }

    .la-ball-fall > div:nth-child(3) {
        -webkit-animation-delay: 0ms;
        -moz-animation-delay: 0ms;
        -o-animation-delay: 0ms;
        animation-delay: 0ms;
    }

    .la-ball-fall.la-sm {
        width: 26px;
        height: 8px;
    }

    .la-ball-fall.la-sm > div {
        width: 4px;
        height: 4px;
        margin: 2px;
    }

    .la-ball-fall.la-2x {
        width: 108px;
        height: 36px;
    }

    .la-ball-fall.la-2x > div {
        width: 20px;
        height: 20px;
        margin: 8px;
    }

    .la-ball-fall.la-3x {
        width: 162px;
        height: 54px;
    }

    .la-ball-fall.la-3x > div {
        width: 30px;
        height: 30px;
        margin: 12px;
    }

    /*
     * Animation
     */
    @-webkit-keyframes ball-fall {
        0% {
            opacity: 0;
            -webkit-transform: translateY(-145%);
            transform: translateY(-145%);
        }
        10% {
            opacity: .5;
        }
        20% {
            opacity: 1;
            -webkit-transform: translateY(0);
            transform: translateY(0);
        }
        80% {
            opacity: 1;
            -webkit-transform: translateY(0);
            transform: translateY(0);
        }
        90% {
            opacity: .5;
        }
        100% {
            opacity: 0;
            -webkit-transform: translateY(145%);
            transform: translateY(145%);
        }
    }

    @-moz-keyframes ball-fall {
        0% {
            opacity: 0;
            -moz-transform: translateY(-145%);
            transform: translateY(-145%);
        }
        10% {
            opacity: .5;
        }
        20% {
            opacity: 1;
            -moz-transform: translateY(0);
            transform: translateY(0);
        }
        80% {
            opacity: 1;
            -moz-transform: translateY(0);
            transform: translateY(0);
        }
        90% {
            opacity: .5;
        }
        100% {
            opacity: 0;
            -moz-transform: translateY(145%);
            transform: translateY(145%);
        }
    }

    @-o-keyframes ball-fall {
        0% {
            opacity: 0;
            -o-transform: translateY(-145%);
            transform: translateY(-145%);
        }
        10% {
            opacity: .5;
        }
        20% {
            opacity: 1;
            -o-transform: translateY(0);
            transform: translateY(0);
        }
        80% {
            opacity: 1;
            -o-transform: translateY(0);
            transform: translateY(0);
        }
        90% {
            opacity: .5;
        }
        100% {
            opacity: 0;
            -o-transform: translateY(145%);
            transform: translateY(145%);
        }
    }

    @keyframes ball-fall {
        0% {
            opacity: 0;
            -webkit-transform: translateY(-145%);
            -moz-transform: translateY(-145%);
            -o-transform: translateY(-145%);
            transform: translateY(-145%);
        }
        10% {
            opacity: .5;
        }
        20% {
            opacity: 1;
            -webkit-transform: translateY(0);
            -moz-transform: translateY(0);
            -o-transform: translateY(0);
            transform: translateY(0);
        }
        80% {
            opacity: 1;
            -webkit-transform: translateY(0);
            -moz-transform: translateY(0);
            -o-transform: translateY(0);
            transform: translateY(0);
        }
        90% {
            opacity: .5;
        }
        100% {
            opacity: 0;
            -webkit-transform: translateY(145%);
            -moz-transform: translateY(145%);
            -o-transform: translateY(145%);
            transform: translateY(145%);
        }
    }
</style>
