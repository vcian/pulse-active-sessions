<x-pulse::card :cols="$cols" :rows="$rows" :class="$class . ' relative'">
    <div class="flex flex-col relative z-10">
        <x-pulse::card-header name="Active Sessions" title="Time: {{ number_format($time) }}ms; Run at: {{ $runAt }};">
            <x-slot:icon>
                <x-pulse_active_session::icons.session />
            </x-slot:icon>
            <x-slot:actions>
                <div class="flex flex-grow">
                    <div class="w-full flex items-center gap-4">
                        <div class="flex flex-wrap gap-4">
                            <div class="h-6 w-6">
                                @if(count($activeSessionThreshold) > 0)
                                    @if ($webLoginCount['total'] >= $activeSessionThreshold['critical']['value'])
                                    <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg" width="407.98699951171875" height="512"
                                        viewBox="0 0 407.987 512">
                                        <g id="fire_01" data-name="fire 01" transform="translate(-52.006)">
                                            <path id="Path_15054" data-name="Path 15054"
                                                d="M373.5,163.809c-10.67-11.649-27.258-27.372-34.315,8.13-2.4,12.071-8.007,21.376-12.812,28.868-5.179-26.212-20.761-53.343-35.443-71.636C285.435,122.327,265.941,95.12,262.2,49.6a8.186,8.186,0,0,0-13.675-5.4c-48.232,43.784-74.706,104-75.689,171.795,0,0-20.084-16.929-30.995-48.423-2.938-8.481-14.308-10.1-19.241-2.6-.947,1.44-1.825,2.882-2.617,4.282-37.213,65.8-55.114,145.7-38.565,220.045,27.67,124.5,210.7,159.3,307.6,81.054C483.822,393.8,453.686,251.311,373.5,163.809Z"
                                                fill="#ed694a" class="svg-elem svg-elem-9"></path>
                                            <g id="Group_6170" dat    a-name="Group 6170">
                                                <path id="Path_15055" data-name="Path 15055"
                                                    d="M107.171,397.118c-16.357-75.135.957-155.818,37.3-222.648-.919-2.217-1.8-4.51-2.629-6.894-2.938-8.481-14.308-10.1-19.241-2.6-.947,1.44-1.824,2.882-2.616,4.282C82.768,235.058,64.867,314.963,81.416,389.3c12.621,56.786,57.569,94.9,111.92,112.149C150.936,480.913,117.732,445.7,107.171,397.118Z"
                                                    fill="#d8553a" class="svg-elem svg-elem-10"></path>
                                                <path id="Path_15056" data-name="Path 15056"
                                                    d="M198.716,214.75c2.009-61.527,24.263-116.7,64.093-159.079-.274-2.334-.477-4.421-.608-6.005-.571-6.851-8.345-10.287-13.37-5.745-46.585,42.118-74.979,101.727-76,172.075l8.719,6.771a11.738,11.738,0,0,0,14.213.135C198.212,220.828,198.563,217.7,198.716,214.75Z"
                                                    fill="#d8553a" class="svg-elem svg-elem-11"></path>
                                            </g>
                                            <path id="Path_15057" data-name="Path 15057"
                                                d="M344.242,254.818c-7.88-8.6-20.133-20.218-25.345,6.005-1.772,8.915-5.915,15.789-9.462,21.323-3.825-19.361-15.335-39.4-26.179-52.912-4.058-5.055-18.456-25.15-21.223-58.771a6.045,6.045,0,0,0-10.1-3.989c-35.625,32.339-55.18,76.819-55.9,126.891,0,0-14.835-12.5-22.893-35.766a7.994,7.994,0,0,0-14.212-1.92c-.7,1.063-1.348,2.129-1.933,3.163-27.487,48.6-40.709,107.62-28.486,162.53,20.438,91.956,155.63,117.665,227.2,59.868C425.73,424.7,403.471,319.45,344.242,254.818Z"
                                                fill="#f4a32c" class="svg-elem svg-elem-4"></path>
                                            <path id="Path_15058" data-name="Path 15058"
                                                d="M153.994,428.585c-10.822-53.676.387-111.271,24.152-159.231a92.26,92.26,0,0,1-5.013-11.754,7.994,7.994,0,0,0-14.212-1.921c-.7,1.063-1.348,2.129-1.933,3.164-27.486,48.6-40.707,107.62-28.486,162.529,9.506,42.768,43.837,71.2,85.041,83.567C184.268,490.258,161.218,464.464,153.994,428.585Z"
                                                fill="#e89528" class="svg-elem svg-elem-5"></path>
                                            <path id="Path_15059" data-name="Path 15059"
                                                d="M317.534,337.9c-5.334-5.824-13.63-13.687-17.158,4.065-1.2,6.036-4,10.688-6.406,14.434-2.59-13.107-10.38-26.672-17.722-35.818-2.746-3.422-12.493-17.025-14.367-39.784a4.092,4.092,0,0,0-6.837-2.7c-24.116,21.892-37.353,52-37.844,85.9,0,0-10.043-8.465-15.5-24.211a5.411,5.411,0,0,0-9.62-1.3c-.474.719-.912,1.441-1.309,2.141-18.606,32.9-27.557,72.852-19.283,110.023,13.835,62.249,105.352,79.652,153.8,40.527a77.921,77.921,0,0,0,28.964-53.16C357.4,406.388,345.232,368.129,317.534,337.9Z"
                                                fill="#f4d44e" class="svg-elem svg-elem-3"></path>
                                            <path id="Path_15060" data-name="Path 15060"
                                                d="M292.749,415.006c-2.972-3.245-7.593-7.625-9.559,2.264a22.9,22.9,0,0,1-3.569,8.042c-1.443-7.3-5.783-14.86-9.873-19.955-1.53-1.907-6.961-9.486-8.005-22.165a2.28,2.28,0,0,0-3.809-1.505c-13.435,12.2-20.811,28.972-21.084,47.856a32.941,32.941,0,0,1-8.634-13.489,3.014,3.014,0,0,0-5.359-.724c-.264.4-.508.8-.728,1.193-10.366,18.33-15.353,40.588-10.743,61.3,10.5,47.231,96.546,45.8,101.824-7.039C314.962,453.16,308.18,431.844,292.749,415.006Z"
                                                fill="#eae9e8" ></path>
                                            <path id="Path_15061" data-name="Path 15061"
                                                d="M231.364,487.194c-4.61-18.525.377-38.436,10.743-54.832.22-.349.465-.708.728-1.067a3.17,3.17,0,0,1,5.359.647,29.631,29.631,0,0,0,8.634,12.067,54.679,54.679,0,0,1,14.488-36.586c-2.065-2.86-8.383-9.756-9.574-24.229a2.28,2.28,0,0,0-3.809-1.505c-13.435,12.2-20.811,28.972-21.084,47.856a32.942,32.942,0,0,1-8.634-13.488,3.014,3.014,0,0,0-5.359-.724c-.264.4-.508.8-.728,1.193-10.366,18.329-15.353,40.588-10.743,61.3,4.266,19.2,21.792,30.731,41.168,33.515C242.1,506.2,234.068,498.077,231.364,487.194Z"
                                                fill="#f7e7a1" class="svg-elem svg-elem-8"></path>
                                            <g id="Group_6171" data-name="Group 6171">
                                                <path id="Path_15062" data-name="Path 15062"
                                                    d="M184.6,68.045A43.825,43.825,0,0,0,184.6,0,43.824,43.824,0,0,0,184.6,68.045Z"
                                                    fill="#d8553a" class="svg-elem svg-elem-12"></path>
                                                <path id="Path_15063" data-name="Path 15063"
                                                    d="M443.793,209.213a43.825,43.825,0,0,0,0-68.045C433.909,149.206,422.216,191.664,443.793,209.213Z"
                                                    fill="#d8553a" class="svg-elem svg-elem-13"></path>
                                                <path id="Path_15064" data-name="Path 15064"
                                                    d="M60.785,222.606a31.585,31.585,0,0,0,0-49.041C53.662,179.358,45.234,209.958,60.785,222.606Z"
                                                    fill="#d8553a" class="svg-elem svg-elem-14"></path>
                                                <path id="Path_15065" data-name="Path 15065"
                                                    d="M330.709,117.086a31.585,31.585,0,0,0,0-49.041A31.586,31.586,0,0,0,330.709,117.086Z"
                                                    fill="#d8553a" class="svg-elem svg-elem-15"></path>
                                            </g>
                                            <path id="Path_15066" data-name="Path 15066"
                                                d="M215.911,285.324c3.579-42.252,19.714-79.821,46.806-108.462q-.411-3.114-.685-6.4a6.045,6.045,0,0,0-10.1-3.989c-28.23,25.626-46.348,58.882-53.022,96.5l-.01,0c-.495,2.79-1.109,6.854-1.5,10.047a.03.03,0,0,0,0,.008,191.267,191.267,0,0,0-1.381,20.343C196.78,293.514,213.848,302.287,215.911,285.324Z"
                                                fill="#e89528" class="svg-elem svg-elem-8"></path>
                                            <g id="Group_6172" data-name="Group 6172">
                                                <path id="Path_15067" data-name="Path 15067"
                                                    d="M196.43,459.481c-7.355-34.725-1.091-71.755,13.461-103.84a62.743,62.743,0,0,1-8.187-15.855,5.412,5.412,0,0,0-9.621-1.3c-.473.719-.913,1.441-1.309,2.141-18.606,32.9-27.557,72.852-19.283,110.023,6.48,29.152,30,48.468,58.148,56.749C213.253,496.521,200.858,480.42,196.43,459.481Z"
                                                    fill="#e8c842" class="svg-elem svg-elem-6"></path>
                                                <path id="Path_15068" data-name="Path 15068"
                                                    d="M243.751,348.824a124.8,124.8,0,0,1,20.784-52.675,86,86,0,0,1-2.652-15.348,4.092,4.092,0,0,0-6.837-2.7,113.419,113.419,0,0,0-31.938,49.428l-.016,0c-.964,2.977-2.327,7.909-3.153,11.8a.163.163,0,0,0,.021-.005A127.163,127.163,0,0,0,217.2,364C230.7,365.681,241.857,362.34,243.751,348.824Z"
                                                    fill="#e8c842" class="svg-elem svg-elem-7"></path>
                                            </g>
                                        </g>
                                    </svg>
                                    @elseif ($webLoginCount['total'] >= $activeSessionThreshold['high']['value'])
                                    <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg" width="407.98699951171875" height="512"
                                        viewBox="0 0 407.987 512">
                                        <g id="fire_02" data-name="fire 02" transform="translate(-52.006)">
                                            <path id="Path_15057" data-name="Path 15057"
                                                d="M408.546,281.067c-10.18-11.115-26.009-26.119-32.743,7.758-2.289,11.517-7.641,20.4-12.224,27.547-4.941-25.012-19.811-50.9-33.82-68.356-5.242-6.53-23.843-32.491-27.418-75.925a7.81,7.81,0,0,0-13.048-5.153c-46.023,41.778-71.286,99.241-72.223,163.928,0,0-19.165-16.154-29.575-46.205-2.8-8.092-13.654-9.636-18.36-2.48-.9,1.373-1.741,2.75-2.5,4.086-35.51,62.787-52.591,139.032-36.8,209.969,26.4,118.8,201.055,152.009,293.518,77.342C513.819,500.528,485.063,364.564,408.546,281.067Z"
                                                transform="translate(-44.425 -101.316)" fill="#f4a32c" class="svg-elem svg-elem-6">
                                            </path>
                                            <path id="Path_15058" data-name="Path 15058"
                                                d="M162.769,480.087c-13.981-69.343.5-143.749,31.2-205.708a119.192,119.192,0,0,1-6.476-15.185c-2.8-8.092-13.654-9.637-18.36-2.482-.9,1.373-1.741,2.75-2.5,4.087-35.509,62.787-52.589,139.032-36.8,209.968,12.281,55.251,56.632,91.977,109.863,107.959C201.879,559.761,172.1,526.438,162.769,480.087Z"
                                                transform="translate(-44.425 -75.85)" fill="#e89528" class="svg-elem svg-elem-7">
                                            </path>
                                            <path id="Path_15059" data-name="Path 15059"
                                                d="M361.065,355.673c-6.891-7.524-17.608-17.682-22.166,5.251-1.549,7.8-5.173,13.808-8.276,18.647-3.346-16.933-13.41-34.457-22.895-46.273-3.547-4.421-16.139-21.994-18.56-51.4a5.286,5.286,0,0,0-8.833-3.488c-31.155,28.282-48.256,67.18-48.89,110.969,0,0-12.974-10.936-20.02-31.278A6.991,6.991,0,0,0,199,356.426c-.612.929-1.178,1.862-1.691,2.766-24.037,42.5-35.6,94.116-24.911,142.137,17.873,80.418,136.1,102.9,198.693,52.356a100.665,100.665,0,0,0,37.418-68.676C412.573,444.146,396.847,394.72,361.065,355.673Z"
                                                transform="translate(-31.446 -68.585)" fill="#f4d44e" class="svg-elem svg-elem-3">
                                            </path>
                                            <path id="Path_15060" data-name="Path 15060"
                                                d="M317,424.906c-3.839-4.192-9.809-9.851-12.349,2.925a29.583,29.583,0,0,1-4.611,10.389c-1.864-9.433-7.471-19.2-12.755-25.779-1.977-2.464-8.993-12.255-10.341-28.635a2.946,2.946,0,0,0-4.921-1.944c-17.356,15.757-26.885,37.428-27.238,61.824a42.556,42.556,0,0,1-11.154-17.426,3.893,3.893,0,0,0-6.923-.935c-.341.518-.656,1.037-.94,1.541-13.392,23.68-19.834,52.435-13.879,79.188,13.562,61.017,124.726,59.167,131.545-9.094C345.7,474.2,336.936,446.658,317,424.906Z"
                                                transform="translate(-19.402 -38.211)" fill="#eae9e8">
                                            </path>
                                            <path id="Path_15061" data-name="Path 15061"
                                                d="M237.7,518.163c-5.956-23.932.487-49.655,13.879-70.836.284-.451.6-.915.94-1.378a4.1,4.1,0,0,1,6.923.836A38.281,38.281,0,0,0,270.6,462.374a70.638,70.638,0,0,1,18.717-47.265c-2.668-3.695-10.83-12.6-12.368-31.3a2.946,2.946,0,0,0-4.921-1.944c-17.356,15.757-26.885,37.428-27.238,61.824a42.557,42.557,0,0,1-11.154-17.425,3.893,3.893,0,0,0-6.923-.935c-.341.518-.656,1.037-.941,1.541-13.392,23.679-19.834,52.435-13.879,79.188,5.511,24.8,28.153,39.7,53.184,43.3C251.57,542.723,241.192,532.223,237.7,518.163Z"
                                                transform="translate(-19.402 -38.21)" fill="#f7e7a1" class="svg-elem svg-elem-2">
                                            </path>
                                            <g id="Group_6171" data-name="Group 6171">
                                                <path id="Path_15062" data-name="Path 15062"
                                                    d="M184.6,68.045A43.825,43.825,0,0,0,184.6,0,43.824,43.824,0,0,0,184.6,68.045Z"
                                                    fill="#f4a32c" class="svg-elem svg-elem-9"></path>
                                                <path id="Path_15063" data-name="Path 15063"
                                                    d="M443.793,209.213a43.825,43.825,0,0,0,0-68.045C433.909,149.206,422.216,191.664,443.793,209.213Z"
                                                    fill="#f4a32c" class="svg-elem svg-elem-10"></path>
                                                <path id="Path_15064" data-name="Path 15064"
                                                    d="M60.785,222.606a31.585,31.585,0,0,0,0-49.041C53.662,179.358,45.234,209.958,60.785,222.606Z"
                                                    fill="#f4a32c" class="svg-elem svg-elem-11"></path>
                                                <path id="Path_15065" data-name="Path 15065"
                                                    d="M330.709,117.086a31.585,31.585,0,0,0,0-49.041A31.586,31.586,0,0,0,330.709,117.086Z"
                                                    fill="#f4a32c" class="svg-elem svg-elem-12"></path>
                                            </g>
                                            <path id="Path_15066" data-name="Path 15066"
                                                d="M221.715,320.477c4.624-54.585,25.468-103.119,60.468-140.12q-.531-4.023-.885-8.265a7.81,7.81,0,0,0-13.048-5.153c-36.47,33.106-59.876,76.069-68.5,124.663l-.013,0c-.639,3.6-1.433,8.855-1.934,12.979a.038.038,0,0,0,.005.01,247.09,247.09,0,0,0-1.784,26.281C197,331.057,219.05,342.391,221.715,320.477Z"
                                                transform="translate(-23.381 -101.316)" fill="#e89528" class="svg-elem svg-elem-8">
                                            </path>
                                            <g id="Group_6172" data-name="Group 6172">
                                                <path id="Path_15067" data-name="Path 15067"
                                                    d="M204.713,495.939c-9.528-44.985-1.413-92.956,17.438-134.521a81.281,81.281,0,0,1-10.606-20.54,7.011,7.011,0,0,0-12.464-1.684c-.613.931-1.183,1.867-1.7,2.774-24.1,42.621-35.7,94.377-24.98,142.531,8.395,37.765,38.86,62.788,75.329,73.516C226.507,543.923,210.449,523.065,204.713,495.939Z"
                                                    transform="translate(-31.594 -50.616)" fill="#e8c842"
                                                    class="svg-elem svg-elem-4"></path>
                                                <path id="Path_15068" data-name="Path 15068"
                                                    d="M251.595,370.038A161.669,161.669,0,0,1,278.52,301.8a111.4,111.4,0,0,1-3.436-19.883,5.3,5.3,0,0,0-8.857-3.5,146.93,146.93,0,0,0-41.374,64.032l-.021-.005c-1.249,3.857-3.015,10.246-4.085,15.289a.213.213,0,0,0,.027-.007,164.734,164.734,0,0,0-3.57,31.966C234.682,391.875,249.141,387.547,251.595,370.038Z"
                                                    transform="translate(-17.173 -68.066)" fill="#e8c842"
                                                    class="svg-elem svg-elem-5"></path>
                                            </g>
                                        </g>
                                    </svg>
                                    @elseif ($webLoginCount['total'] >= $activeSessionThreshold['medium']['value'])
                                    <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg" width="407.98699951171875" height="512"
                                        viewBox="0 0 407.987 512">
                                        <g id="fire_03" data-name="fire 03" transform="translate(-52.006)">
                                            <path id="Path_15059" data-name="Path 15059"
                                                d="M462.29,396.993c-10.511-11.477-26.859-26.972-33.812,8.011-2.363,11.895-7.89,21.062-12.624,28.444-5.1-25.829-20.455-52.56-34.923-70.583-5.411-6.743-24.619-33.55-28.312-78.4a8.063,8.063,0,0,0-13.473-5.321c-47.523,43.141-73.608,102.476-74.576,169.27,0,0-19.791-16.681-30.539-47.711-2.895-8.355-14.1-9.95-18.957-2.562-.934,1.417-1.8,2.84-2.58,4.219-36.665,64.833-54.3,143.563-38,216.813C201.759,741.843,382.1,776.137,477.578,699.037c32.695-26.4,52.966-63.606,57.077-104.758C540.86,531.948,516.872,456.555,462.29,396.993Z"
                                                transform="translate(-98.902 -228.069)" fill="#f4d44e" class="svg-elem svg-elem-3">
                                            </path>
                                            <path id="Path_15060" data-name="Path 15060"
                                                d="M373.4,447.926c-5.857-6.395-14.963-15.026-18.837,4.461-1.316,6.627-4.394,11.735-7.033,15.848-2.844-14.389-11.4-29.283-19.456-39.324-3.015-3.758-13.717-18.693-15.775-43.679a4.494,4.494,0,0,0-7.506-2.966c-26.475,24.036-41.011,57.093-41.549,94.306,0,0-11.026-9.293-17.014-26.582a5.939,5.939,0,0,0-10.56-1.427c-.52.79-1,1.582-1.435,2.351C213.8,487.037,203.975,530.9,213.06,571.708c20.688,93.074,190.255,90.252,200.656-13.871C417.168,523.113,403.8,481.107,373.4,447.926Z"
                                                transform="translate(-58.849 -127.065)" fill="#eae9e8">
                                            </path>
                                            <path id="Path_15061" data-name="Path 15061"
                                                d="M252.43,590.179c-9.085-36.506.743-75.743,21.17-108.053.434-.688.916-1.4,1.435-2.1,2.708-3.683,8.949-2.889,10.56,1.275a58.392,58.392,0,0,0,17.014,23.779,107.75,107.75,0,0,1,28.55-72.1c-4.069-5.636-16.52-19.225-18.867-47.746a4.494,4.494,0,0,0-7.506-2.966c-26.475,24.036-41.011,57.093-41.548,94.306,0,0-11.026-9.293-17.014-26.58a5.939,5.939,0,0,0-10.56-1.427c-.52.79-1,1.582-1.435,2.351-20.427,36.119-30.255,79.983-21.17,120.793,8.407,37.826,42.944,60.559,81.126,66.045C273.588,627.642,257.758,611.625,252.43,590.179Z"
                                                transform="translate(-58.85 -127.063)" fill="#f7e7a1" class="svg-elem svg-elem-2">
                                            </path>
                                            <g id="Group_6171" data-name="Group 6171">
                                                <path id="Path_15062" data-name="Path 15062"
                                                    d="M184.6,68.045A43.825,43.825,0,0,0,184.6,0,43.824,43.824,0,0,0,184.6,68.045Z"
                                                    fill="#f4d44e" class="svg-elem svg-elem-6"></path>
                                                <path id="Path_15063" data-name="Path 15063"
                                                    d="M443.793,209.213a43.825,43.825,0,0,0,0-68.045C433.909,149.206,422.216,191.664,443.793,209.213Z"
                                                    fill="#f4d44e" class="svg-elem svg-elem-7"></path>
                                                <path id="Path_15064" data-name="Path 15064"
                                                    d="M60.785,222.606a31.585,31.585,0,0,0,0-49.041C53.662,179.358,45.234,209.958,60.785,222.606Z"
                                                    fill="#f4d44e" class="svg-elem svg-elem-8"></path>
                                                <path id="Path_15065" data-name="Path 15065"
                                                    d="M330.709,117.086a31.585,31.585,0,0,0,0-49.041A31.586,31.586,0,0,0,330.709,117.086Z"
                                                    fill="#f4d44e" class="svg-elem svg-elem-9"></path>
                                            </g>
                                            <g id="Group_6172" data-name="Group 6172">
                                                <path id="Path_15067" data-name="Path 15067"
                                                    d="M223.793,579.922c-14.534-68.619-2.156-141.794,26.6-205.2A123.984,123.984,0,0,1,234.215,343.4c-2.9-8.379-14.137-9.977-19.012-2.569-.935,1.421-1.8,2.848-2.587,4.231-36.767,65.013-54.455,143.961-38.1,217.414,12.8,57.607,59.276,95.776,114.9,112.14C257.037,653.116,232.543,621.3,223.793,579.922Z"
                                                    transform="translate(-99.128 -169.63)" fill="#e8c842"
                                                    class="svg-elem svg-elem-4"></path>
                                                <path id="Path_15068" data-name="Path 15068"
                                                    d="M269.663,418.9c4.818-34.433,18.9-72.372,41.071-104.09a169.929,169.929,0,0,1-5.241-30.329,8.086,8.086,0,0,0-13.51-5.335,224.124,224.124,0,0,0-63.112,97.674l-.032-.008c-1.9,5.883-4.6,15.629-6.231,23.322a.325.325,0,0,0,.041-.01,251.284,251.284,0,0,0-5.446,48.76C243.865,452.215,265.92,445.613,269.663,418.9Z"
                                                    transform="translate(-51.487 -227.279)" fill="#e8c842"
                                                    class="svg-elem svg-elem-5"></path>
                                            </g>
                                        </g>
                                    </svg>
                                    @elseif ($webLoginCount['total'] >= $activeSessionThreshold['low']['value'])
                                    <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg" width="407.9880065917969"
                                        height="511.78900146484375" viewBox="0 0 407.988 511.789">
                                        <g id="fire04" transform="translate(-52.006)">
                                            <path id="Path_15060" data-name="Path 15060"
                                                d="M490.451,495.709c-10.044-10.966-25.66-25.768-32.3,7.651-2.258,11.365-7.536,20.125-12.061,27.177-4.877-24.677-19.543-50.218-33.365-67.437-5.171-6.445-23.524-32.057-27.052-74.905a7.706,7.706,0,0,0-12.872-5.086c-45.4,41.219-70.33,97.909-71.252,161.727,0,0-18.908-15.937-29.178-45.585-2.768-7.986-13.471-9.51-18.11-2.447-.892,1.355-1.717,2.714-2.46,4.032C216.763,562.78,199.91,638,215.489,707.985,250.967,867.6,541.761,862.76,559.6,684.2,565.519,624.648,542.6,552.612,490.451,495.709Z"
                                                transform="translate(-133.254 -311.346)" fill="#f8e284" class="svg-elem svg-elem-2"></path>
                                            <path id="Path_15061" data-name="Path 15061"
                                                d="M283.006,739.66c-15.579-62.6,1.274-129.892,36.305-185.3.743-1.179,1.571-2.393,2.46-3.606,4.643-6.316,15.346-4.954,18.11,2.187,10.27,26.522,29.178,40.78,29.178,40.78.76-46.907,17.752-89.488,48.961-123.64-6.979-9.665-28.33-32.97-32.355-81.881a7.706,7.706,0,0,0-12.872-5.086c-45.4,41.219-70.33,97.909-71.252,161.727,0,0-18.908-15.937-29.178-45.582-2.764-7.986-13.47-9.51-18.11-2.447-.892,1.355-1.717,2.714-2.46,4.032-35.031,61.942-51.885,137.165-36.305,207.15,14.417,64.869,73.645,103.854,139.125,113.262C319.291,803.906,292.144,776.438,283.006,739.66Z"
                                                transform="translate(-133.256 -311.342)" fill="#f4d44e"
                                                class="svg-elem svg-elem-1"></path>
                                            <g id="Group_6171" data-name="Group 6171">
                                                <path id="Path_15062" data-name="Path 15062"
                                                    d="M184.6,68.045A43.825,43.825,0,0,0,184.6,0,43.824,43.824,0,0,0,184.6,68.045Z"
                                                    fill="#f8e284" class="svg-elem svg-elem-3"></path>
                                                <path id="Path_15063" data-name="Path 15063"
                                                    d="M443.793,209.213a43.825,43.825,0,0,0,0-68.045C433.909,149.206,422.216,191.664,443.793,209.213Z"
                                                    fill="#f8e284" class="svg-elem svg-elem-4"></path>
                                                <path id="Path_15064" data-name="Path 15064"
                                                    d="M60.785,222.606a31.585,31.585,0,0,0,0-49.041C53.662,179.358,45.234,209.958,60.785,222.606Z"
                                                    fill="#f8e284" class="svg-elem svg-elem-5"></path>
                                                <path id="Path_15065" data-name="Path 15065"
                                                    d="M330.709,117.086a31.585,31.585,0,0,0,0-49.041A31.586,31.586,0,0,0,330.709,117.086Z"
                                                    fill="#f8e284" class="svg-elem svg-elem-6"></path>
                                            </g>
                                        </g>
                                    </svg>
                                    @endif
                                @endif
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 font-medium">
                                <div class="h-0.5 w-3 rounded-full bg-[#9333ea]"></div>
                                Api
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 font-medium">
                                <div class="h-0.5 w-3 rounded-full bg-[#eab308]"></div>
                                Web
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                @if ($filters)
                                <select wire:change="filterByProviders(event.target.value)"
                                    class="overflow-ellipsis w-full border-0 pl-3 pr-8 py-1 bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs sm:text-sm shadow-none focus:ring-0">
                                    @foreach ($filters as $filter)
                                        <option value="{{ $filter }}">{{ ucfirst($filter) }}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </x-slot:actions>
        </x-pulse::card-header>
        <x-pulse::scroll :expand="$expand" wire:poll.5s="">
            <div class="h-2 first:h-0"></div>
            @isset($webLoginCount['total'])
            <div class="flex justify-between mt-3 mb-3">
                <div class="">
                    <div class="whitespace-nowrap tabular-nums">
                        <span class="session-label web-label text-xs">Web</span>
                        <span class="text-sm">{{ $webLoginCount['total'] != 0 ? round(($webLoginCount['web'] / $webLoginCount['total']) * 100, 2)  : 0}}%</span>
                    </div>
                    <div class="h-2 first:h-0"></div>
                    <div class="whitespace-nowrap tabular-nums">
                        <span class="session-label api-label text-xs">Api</span>
                        <span class="text-sm">{{ $webLoginCount['total'] != 0 ? round(($webLoginCount['api'] / $webLoginCount['total']) * 100, 2) : 0 }}%</span>
                    </div>
                </div>
                <div class="">
                    <div wire:ignore x-data="storageChartDocker({
                        total: {{ $webLoginCount['total'] }},
                        web: {{ $webLoginCount['web'] }},
                        api: {{ $webLoginCount['api'] }},
                    })">
                        <canvas x-ref="canvas" width="70" height="70" class=""></canvas>
                    </div>
                </div>
            </div>
            @endisset
            @if (!count($webLoginCount))
            <x-pulse::no-results />
            @else
            <div class="grid grid-cols-1 @lg:grid-cols-2 @3xl:grid-cols-3 @6xl:grid-cols-4 gap-2">
                <x-pulse::table>
                    <colgroup>
                        <col width="100%" />
                        <col width="0%" />
                        <col width="0%" />
                    </colgroup>
                    <x-pulse::thead>
                        <tr>
                            <x-pulse::th>Platform</x-pulse::th>
                            <x-pulse::th class="text-right">Count</x-pulse::th>
                        </tr>
                    </x-pulse::thead>
                    <tbody>
                        <tr class="h-2 first:h-0"></tr>
                        <tr wire:key="{{ $webLoginCount['web'] }}">
                            <x-pulse::td class="max-w-[1px]">
                                <code class="block text-xs text-gray-900 dark:text-gray-100 truncate" title="">
                                    Web ({{ config('session.driver') }})
                                </code>

                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                {{ $webLoginCount['web'] }}
                            </x-pulse::td>
                        </tr>
                        <tr class="h-2 first:h-0"></tr>
                        <tr wire:key="{{ $webLoginCount['web'] }}">
                            <x-pulse::td class="max-w-[1px]">
                                <code class="block text-xs text-gray-900 dark:text-gray-100 truncate" title="">
                                 Api ({{ $webLoginCount['api_driver'] }})
                                </code>

                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                {{ $webLoginCount['api'] }}
                            </x-pulse::td>
                        </tr>
                    </tbody>
                </x-pulse::table>
            </div>
            @endif
            <hr class="mt-4 mb-3 border-gray-200 dark:border-gray-700">
            <x-pulse::card-header name="Sessions" title="Time: {{ number_format($time) }}ms; Run at: {{ $runAt }};"
                details="past {{ $this->periodForHumans() }}">
            </x-pulse::card-header>
            @if ($session->isEmpty())
            <x-pulse::no-results />
            @else
            <div class="grid gap-3 mx-px mb-px">
                @foreach ($session as $queue => $readings)
                <div wire:key="{{ $queue }}">
                    @php
                    $highest = $readings->flatten()->max();
                    @endphp

                    <div class="mt-3 relative">
                        <div
                            class="absolute -left-px -top-2 max-w-fit h-4 flex items-center px-1 text-xs leading-none text-white font-bold bg-purple-500 rounded after:[--triangle-size:4px] after:border-l-purple-500 after:absolute after:right-[calc(-1*var(--triangle-size))] after:top-[calc(50%-var(--triangle-size))] after:border-t-[length:var(--triangle-size)] after:border-b-[length:var(--triangle-size)] after:border-l-[length:var(--triangle-size)] after:border-transparent">
                            {{ number_format($highest) }}
                        </div>
                        <div wire:ignore class="h-14" x-data="sessionChart({
                                queue: '{{ $queue }}',
                                readings: @js($readings),
                                sampleRate: 1,
                            })">
                            <canvas x-ref="canvas"
                                class="ring-1 ring-gray-900/5 dark:ring-gray-100/10 bg-gray-50 dark:bg-gray-800 rounded-md shadow-sm"></canvas>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </x-pulse::scroll>
    </div>
    @if(isset($color) && !empty($color))
    <div class="absolute bottom-0 left-0 w-full h-10 z-0 overflow-hidden rounded-xl">
        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
            viewBox="0 24 150 28" preserveAspectRatio="none">
            <defs>
                <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
            </defs>
            <g class="waves">
                <use xlink:href="#gentle-wave" x="50" y="0" fill="{{ $color }}" fill-opacity=".2" />
                <use xlink:href="#gentle-wave" x="50" y="3" fill="{{ $color }}" fill-opacity=".25" />
                <use xlink:href="#gentle-wave" x="50" y="6" fill="{{ $color }}" fill-opacity=".30" />
            </g>
        </svg>
    </div>
    @endif
</x-pulse::card>

@script
<script>
let api_driver = "{{ $webLoginCoun['api_driver'] ?? 'sanctum' }}"
Alpine.data('storageChartDocker', (config) => ({
    init() {
        let chart = new Chart(
            this.$refs.canvas,
            {
                type: 'doughnut',
                data: {
                    labels: ['Api', 'Web'],
                    datasets: [
                        {
                            data: [
                                config.api,
                                config.web,
                            ],
                            backgroundColor: [
                                '#9333ea',
                                '#eab308',
                            ],
                            hoverBackgroundColor: [
                                '#9333ea',
                                '#eab308',
                            ],
                            hoverOffset: 4
                        },
                    ],
                },
                options: {
                    borderWidth: 0,
                    plugins: {
                        legend: {
                            display: false,
                            position: 'right',
                        },
                        tooltip: {
                            enabled: true,
                            intersect: false,
                            mode: 'nearest',
                            callbacks: {
                                label: function(item) {
                                    return (item.dataset.data.reduce((a, b) => a + b, 0) != 0)  ? parseFloat(item.parsed / item.dataset.data.reduce((a, b) => a + b, 0) * 100).toFixed(2) + '%' : 0;
                                }
                            },
                            displayColors: false,
                        }
                    },
                },
            }
        )

        Livewire.on('servers-chart-update-session', ({ servers }) => {
            const storage = servers;

            if (chart === undefined) {
                return
            }

            if (storage === undefined && chart) {
                chart.destroy()
                chart = undefined
                return
            }

            chart.data.datasets[0].data = [
                storage.api,
                storage.web,
            ]
            chart.update()
        })
    }
}))

Alpine.data('sessionChart', (config) => ({
    init() {
        let chart = new Chart(
            this.$refs.canvas, {
                type: 'line',
                data: {
                    labels: this.labels(config.readings),
                    datasets: [{
                            label: 'Web Login',
                            borderColor: '#eab308',
                            data: this.scale(config.readings.login_hit),
                            order: 1,
                        },
                        {
                            label: api_driver + ' Login',
                            borderColor: '#9333ea',
                            data: this.scale(config.readings.api_hit),
                            order: 2,
                        }
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    layout: {
                        autoPadding: false,
                        padding: {
                            top: 1,
                        },
                    },
                    datasets: {
                        line: {
                            borderWidth: 2,
                            borderCapStyle: 'round',
                            pointHitRadius: 10,
                            pointStyle: false,
                            tension: 0.2,
                            spanGaps: false,
                            segment: {
                                borderColor: (ctx) => ctx.p0.raw === 0 && ctx.p1.raw === 0 ?
                                    'transparent' : undefined,
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: false,
                        },
                        y: {
                            display: false,
                            min: 0,
                            max: this.highest(config.readings),
                        },
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            mode: 'index',
                            position: 'nearest',
                            intersect: false,
                            callbacks: {
                                beforeBody: (context) => context
                                    .map(item =>
                                        `${item.dataset.label}: ${1 < 1 ? '~' : ''}${item.formattedValue}`
                                    )
                                    .join(', '),
                                label: () => null,
                            },
                        },
                    },
                },
            }
        )

        Livewire.on('session-chart-update', ({
            session
        }) => {
            if (chart === undefined) {
                return
            }

            if (session[config.queue] === undefined && chart) {
                chart.destroy()
                chart = undefined
                return
            }

            chart.data.labels = this.labels(session[config.queue])
            chart.options.scales.y.max = this.highest(session[config.queue])
            chart.data.datasets[0].data = this.scale(session[config.queue].login_hit)
            chart.data.datasets[1].data = this.scale(session[config.queue].api_hit)
            chart.update()
        })
    },
    labels(readings) {
        return Object.keys(readings.login_hit)
    },
    scale(data) {
        return Object.values(data).map(value => value * (1 / 1))
    },
    highest(readings) {
        return Math.max(...Object.values(readings).map(dataset => Math.max(...Object.values(
            dataset)))) * (1 / 1)
    }
}))
</script>
@endscript
