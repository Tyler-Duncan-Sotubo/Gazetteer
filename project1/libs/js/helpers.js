export default class Helpers {
  static testing() {
    console.log("Testing");
  }
  static convertDate = (date) => {
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

  static formatResult = (number) => {
    const roundedNumber = Number(number).toFixed(2);
    const formattedNumber = Number(roundedNumber).toLocaleString();
    return formattedNumber;
  };

  static errorHandler = (status) => {
    switch (status) {
      case "timeout":
        console.log("AJAX Error: Timeout");
        break;
      case "error":
        console.log("AJAX Error: General Error");
        break;
      case "abort":
        console.log("AJAX Error: Request Aborted");
        break;
      case "parsererror":
        console.log("AJAX Error: JSON Parse Error");
        break;
      default:
        console.log("AJAX Error: Unknown Error");
        break;
    }
  };
}
