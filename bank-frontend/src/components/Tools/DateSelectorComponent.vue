<template>
  <div>
    <q-select
      v-model="currentValue"
      :options="dateOptions"
      option-value="option"
      option-label="label"
      label-color="grey-3"
      color="grey-3"
      @update:model-value="(val) => changeDate(val)"
    />
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import { useConfigStore } from 'src/stores/config';
import { IOption } from 'src/models/shared/baseinterfaces';
export default defineComponent({
  name: 'DateSelectorComponent',

  setup(props, context) {
    let currentDate = ref(new Date());
    const store = useConfigStore();

    let dateOptions = [] as IOption[];
    for (let i = 0; i <= 12; i++) {
      let monthName = currentDate.value.toLocaleString('default', {
        month: 'long',
      });
      dateOptions.push({
        option: currentDate.value.toISOString().split('T')[0],
        label: monthName + ', ' + currentDate.value.getFullYear(),
      });
      currentDate.value.setMonth(currentDate.value.getMonth() - 1);
    }

    let dateOption = dateOptions[0];

    if (store.hasCurrentDate) {
      dateOption = {
        option: store.getCurrentDate.option,
        label: store.getCurrentDate.label,
      };
    } else {
      store.setCurrentDate(dateOption);
    }

    const currentValue = ref(dateOption);

    function changeDate(currentSelected: IOption) {
      store.setCurrentDate(currentSelected);
      context.emit('dateSelectedChange', currentSelected);
    }

    return {
      dateOptions,
      currentValue,
      changeDate,
    };
  },
  dateSelectedChange: ['dateSelectedChange'],
});
</script>
<style lang="scss" scoped>
.align-div {
  text-align: end;
}

.q-field__native {
  color: white !important;
}
</style>
