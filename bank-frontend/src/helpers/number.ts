const formatter = new Intl.NumberFormat('en-US', {
  style: 'currency',
  currency: 'USD',
});

function toCurrency(value: number): string {
  return formatter.format(value);
}

export default {
  toCurrency,
};
