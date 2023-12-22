function calculateItemsNotCompleted (...args: number[]): number {
  let count = 0
  for (const num of args) {
    if (num !== 100) {
      count++
    }
  }
  return count
}

export default calculateItemsNotCompleted
