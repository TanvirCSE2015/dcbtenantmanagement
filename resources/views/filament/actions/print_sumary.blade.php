<div
    x-data="{
        type: 'list',

        print() {
            const url = @js($url);

            const separator = url.includes('?') ? '&' : '?';

            window.open(
                url + separator + 'type=' + this.type,
                '_blank',
                'noopener,noreferrer'
            );
        }
    }"
>
    <div class="space-y-4">

        <div>
            <label class="text-sm font-medium">
                প্রিন্টের ধরন নির্বাচন করুন
            </label>

            <div class="mt-3 space-y-2" style="margin: 20px 0px;">

                <label class="flex items-center gap-2">
                    <input
                        type="radio"
                        value="list"
                        x-model="type"
                    >
                    <span>তালিকা</span>
                </label>

                <label class="flex items-center gap-2">
                    <input
                        type="radio"
                        value="details"
                        x-model="type"
                    >
                    <span>বিস্তারিত</span>
                </label>

            </div>
        </div>

        <div class="flex justify-end">
            <button
                type="button"
                x-on:click="print()"
                class="fi-btn fi-btn-size-md fi-color-success" style="background-color: forestgreen; color:white;"
            >
                <span>প্রিন্ট করুন</span>
            </button>
        </div>

    </div>
</div>