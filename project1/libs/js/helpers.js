export const convertDate = (date) => {
  const milliseconds = date * 1000;
  const newDate = new Date(milliseconds);
  const formattedDate = newDate.toLocaleString();
  const fdate = new Date(
    formattedDate.replace(
      /(\d{2})\/(\d{2})\/(\d{4}), (\d{2}):(\d{2}):(\d{2})/,
      "$3-$2-$1T$4:$5:$6"
    )
  );
  const dayOfWeekNumber = fdate.getDay();
  const daysOfWeek = ["Sun", "Mon", "Tues", "Wed", "Thur", "Fri", "Sat"];
  const dayOfWeekName = daysOfWeek[dayOfWeekNumber];
  return dayOfWeekName;
};

export const formatResult = (number) => {
  const roundedNumber = Number(number).toFixed(2);
  const formattedNumber = Number(roundedNumber).toLocaleString();
  return formattedNumber;
};
