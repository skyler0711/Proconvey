const useDownload = () => {
  return async (url: string) => {
    const tempLink = document.createElement('a')
    tempLink.style.display = 'none'
    tempLink.href = url
    tempLink.setAttribute('download', 'file')

    if (typeof tempLink.download === 'undefined') {
      tempLink.setAttribute('target', '_blank')
    }

    document.body.appendChild(tempLink)
    tempLink.click()

    setTimeout(() => {
      document.body.removeChild(tempLink)
    }, 200)
  }
}

export default useDownload
