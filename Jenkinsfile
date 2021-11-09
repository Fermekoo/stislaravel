pipeline {
    agent { label 'master' }
    stages {
        stage ('#'){
            steps {
                script {
                    def remote = [:]
                    remote.name = "staging-server"
                    remote.host = "10.35.53.58"
                    remote.allowAnyHosts = true
                    
                    withCredentials([sshUserPrivateKey(credentialsId: 'ssh_adminaja', keyFileVariable: 'privatekey', usernameVariable: 'username')]) {
                        remote.user = username
                        remote.identityFile = privatekey
                        
                        stage("Get & Move .env") {
                            sshCommand remote: remote, command: 'pwd; cd ~/ponpapuaxx; git pull origin master'
                            sshCommand remote: remote, command: 'cp ~/ponpapuaxx/hris-staging/.env ~/stislaravel/.env'
                        }
                        stage("Get Staging Branch") {
                            sshCommand remote: remote, command: 'cd ~/stislaravel; pwd; git checkout staging; git pull origin staging'
                        }
                        stage("Re-run") {
                            sshCommand remote: remote, command: 'cd ~/stislaravel; docker-compose down; docker-compose up -d; sleep 3; docker-compose ps;'
                        }
                    }
                }
            }
        }
    }
}
