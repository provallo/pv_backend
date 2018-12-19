const pkg = require('../plugin')
const { exec } = require('child_process')

const TARGET_CHANNEL = 'stable'
const TARGET_PLATFORM = 'provallo-plugin'

console.log('Creating zip...')
exec('vallo create-zip --json .', (err, result) => {
    if (err) {
        console.log(err)
    } else {
        let { filename } = JSON.parse(result)
        
        console.log('Creating release...')
        exec('savas create-release ' + pkg.version + ' --channel="' + TARGET_CHANNEL + '"', (err, result) => {
            if (err) {
                console.log(err)
            } else {
                if (result === 'the release were created successfully' || result.indexOf('The provided version is already used') > -1) {
                    console.log('Uploading file...')
                    
                    exec('savas upload ' + filename + ' ' + pkg.version + ' --channel="' + TARGET_CHANNEL + '" --platform="' + TARGET_PLATFORM + '"', (err, result) => {
                        if (err) {
                            console.log(err)
                        } else {
                            console.log(result)
                        }
                    })
                } else {
                    console.log(result)
                }
            }
        })
    }
})